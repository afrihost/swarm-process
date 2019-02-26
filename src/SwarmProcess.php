<?php
namespace Afrihost\SwarmProcess;

use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Process;

class SwarmProcess extends SwarmProcessBase
{
    /** @var int */
    protected $maxRunStackSize = 10;

    /** @var array */
    protected $queue = array();

    /** @var array */
    private $currentRunningStack = array();

    /** @var int */
    private $runningProcessKeyTracker = 0;

    /** @var integer */
    private $successfulProcessCount = 0;

    /**
     * Runs all the processes, not going over the maxRunStackSize, and continuing until all processes in the processingStack has run their course.
     *
     * @param callable $moreWorkToAddCallable
     * @param callable $shouldContinueRunningCallable
     */
    public function run(callable $moreWorkToAddCallable = null, callable $shouldContinueRunningCallable = null)
    {
        $this->runningProcessKeyTracker = 0; // seed the key

        // As long as we have more thing we can do, do them:
        do {
            // Check if the user specified a process adder callable:
            if (is_callable($moreWorkToAddCallable)) {
                // As long as the callable returns us a process to add, we'll add more. It's up to the user to limit this.
                while ($p = call_user_func($moreWorkToAddCallable)) {
                    $this->pushProcessOnQueue($p);
                }
            }
        } while ($this->tick() || (is_callable($shouldContinueRunningCallable) ? call_user_func($shouldContinueRunningCallable) : false));
    }

    /**
     * Does the necessary work to figure out whether a process is done and should be removed from the runningStack as well as adding the next process(es) in line into empty slot(s)
     * If there's more work to be done at the end of the method, tick returns true (so you can use it in your while loop)
     */
    public function tick()
    {
        // Loop through the running things to check if they're done:
        foreach ($this->currentRunningStack as $runningProcessKey => $runningProcess) {
            /** @var $runningProcess Process */

            if ($this->getConfiguration()->isEnforceProcessTimeouts()) {
                if ($runningProcess->isRunning()) {
                    try {
                        $runningProcess->checkTimeout();
                    } catch (ProcessTimedOutException $e) {
                        // do nothing, just log as checkTimeout() internally stops the process
                        $logMessage =  '- Killed Process ' . $runningProcessKey . ' from currentRunningStack. Timeout of '.$runningProcess->getTimeout().' reached';
                        $this->logger->warning($logMessage);
                    }
                }
            }

            if (!$runningProcess->isRunning()) {
                $logMessage =  '- Removed Process ' . $runningProcessKey . ' from currentRunningStack - '.
                    'ExitCode:'.$runningProcess->getExitCode().'('.$runningProcess->getExitCodeText().') '.
                    '[' . count($this->queue) . ' left in queue]';
                if($runningProcess->isSuccessful()) {
                    $this->successfulProcessCount++;
                }
                unset($this->currentRunningStack[$runningProcessKey]);
                $this->logger->info($logMessage);

                if (is_callable($this->getConfiguration()->getCompletedCallback())) {
                    call_user_func($this->getConfiguration()->getCompletedCallback(), $runningProcess);
                }
            }
        }

        $this->tickFillOpenSlots();

        return ((count($this->queue) > 0) || count($this->currentRunningStack) > 0);
    }

    /**
     * Returns true/false whether we have slots available to add more jobs in concurrency
     *
     * @return bool
     */
    protected function haveRunningSlotsAvailable()
    {
        return (count($this->currentRunningStack) < $this->maxRunStackSize);
    }

    /**
     * Returns the number of elements still left to do on the queue
     *
     * @return int
     */
    public function getStackCount()
    {
        return count($this->queue);
    }

    /**
     * Returns the number of currently running processes
     *
     * @return int
     */
    public function getCurrentRunningStackCount()
    {
        return count($this->currentRunningStack);
    }

    /**
     * Pushes a native command, ex "ls -lahtr" on the processing stack after converting it to a Process object
     *
     * @param string $cmd
     * @return SwarmProcess
     */
    public function pushNativeCommandOnQueue($cmd)
    {
        $tmp = new Process($cmd);

        return $this->pushProcessOnQueue($tmp);
    }

    /**
     * Pushes a Process object on to the processing stack
     *
     * @param Process $process
     * @return $this
     */
    public function pushProcessOnQueue(Process $process)
    {
        $this->queue[] = $process;

        $this->logger->debug('Process pushed on to stack. Stack size: ' . count($this->queue));

        return $this;
    }

    /**
     * Gets the maximum number of processes that can be run at the same time (concurrently)
     *
     * @return int
     */
    public function getMaxRunStackSize()
    {
        return $this->maxRunStackSize;
    }

    /**
     * Set the maximum number of processes that can be run at the same time (concurrently)
     *
     * @param int $maxRunStackSize
     *
     * @return SwarmProcess
     * @throws \OutOfBoundsException
     */
    public function setMaxRunStackSize($maxRunStackSize)
    {
        if ($maxRunStackSize <= 0) {
            throw new \OutOfBoundsException('You many not have a maxRunStack size less or equal to 0. You gave: "' . $maxRunStackSize . '"');
        }

        $this->maxRunStackSize = $maxRunStackSize;

        $this->logger->debug('$maxRunStackSize changed to ' . $maxRunStackSize);

        return $this;
    }

    /**
     * Get the number of successful processes that have completed.
     *
     * @return int
     */
    public function getSuccessfulProcessCount()
    {
        return $this->successfulProcessCount;
    }

    /**
     * Fills any open slots in the run-stack with new processes, if there are available in the queue
     */
    protected function tickFillOpenSlots()
    {
        while ($this->haveRunningSlotsAvailable() && (count($this->queue) > 0)) {
            /** @var Process $tmpProcess */
            $tmpProcess = array_shift($this->queue);
            $tmpProcess->start();
            $this->currentRunningStack[++$this->runningProcessKeyTracker] = $tmpProcess;
            $this->logger->info('+ Started Process ' . $this->runningProcessKeyTracker . ' [' . $tmpProcess->getCommandLine() . ']');
        }
    }

}
