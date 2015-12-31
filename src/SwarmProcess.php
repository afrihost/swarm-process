<?php
/**
 * User: sarel
 * Date: 2015/12/29
 * Time: 17:48
 */

namespace Afrihost\SwarmProcess;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

class SwarmProcess
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var int */
    protected $maxRunStackSize = 10;

    /** @var array */
    protected $queue = array();

    /** @var array */
    private $currentRunningStack = array();

    /** @var int */
    private $runningProcessKeyTracker = 0;

    /**
     * SwarmProcess constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

        $this->logger->debug('__construct(ed) SwarmProcess');
    }

    /**
     * Runs all the processes, not going over the maxRunStackSize, and continuing until all processes in the processingStack has run their course.
     */
    public function run()
    {
        $this->runningProcessKeyTracker = 0; // seed the key

        // As long as we have more thing we can do, do them:
        while ($this->tick()) {
            // the real work sits in the tick() method
        }
    }

    /**
     * Does the necessary work to figure out whether a process is done and should be removed from the runningStack as well as adding the next process(es) in line into empty slot(s)
     * If there's more work to be done at the end of the method, tick returns true (so you can use it in your while loop)
     */
    public function tick()
    {
        // If we have an open slot, use it:
        while ($this->haveRunningSlotsAvailable() && ($this->getStackCount() > 0)) {
            /** @var Process $tmpProcess */
            $tmpProcess = array_shift($this->queue);
            $tmpProcess->start();
            $this->currentRunningStack[++$this->runningProcessKeyTracker] = $tmpProcess;
            $this->logger->info('+ Started Process ' . $this->runningProcessKeyTracker . ' [' . $tmpProcess->getCommandLine() . ']');
        }

        // Loop through the running things to check if they're done:
        foreach ($this->currentRunningStack as $runningProcessKey => $runningProcess) {
            /** @var $runningProcess Process */
            if (!$runningProcess->isRunning()) {
                unset($this->currentRunningStack[$runningProcessKey]);
                $this->logger->info('- Removed Process ' . $runningProcessKey . ' from currentRunningStack [' . count($this->queue) . ' left in queue]');
            }
        }

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
    public function pushNativeCommandOnStack($cmd)
    {
        $tmp = new Process($cmd);

        return $this->pushProcessOnStack($tmp);
    }

    /**
     * Pushes a Process object on to the processing stack
     *
     * @param Process $process
     * @return $this
     */
    public function pushProcessOnStack(Process $process)
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
     * @return SwarmProcess
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


}
