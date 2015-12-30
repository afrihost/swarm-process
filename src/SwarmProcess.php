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
    protected $processingStack = array();

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
    /**
     * Runs all the processes, not going over the maxRunStackSize, and continuing until all processes in the processingStack has run their course.
     */
    public function run()
    {
        $this->runningProcessKeyTracker = 0; // seed the key

        // As long as we have more thing we can do, do them:
        while (count($this->processingStack) > 0) {
            // If we have an opne slot, use it:
            while (count($this->currentRunningStack) < $this->maxRunStackSize) {
                /** @var Process $tmpProcess */
                $tmpProcess = array_shift($this->processingStack);
                $tmpProcess->start();
                $this->currentRunningStack[++$this->runningProcessKeyTracker] = $tmpProcess;
                $this->logger->info('+ Started Process ' . $this->runningProcessKeyTracker . ' [' . $tmpProcess->getCommandLine() . ']');
            }

            // Loop through the running things to check if they're done:
            foreach ($this->currentRunningStack as $runningProcessKey => $runningProcess) {
                /** @var $runningProcess Process */
                if (!$runningProcess->isRunning()) {
                    unset($this->currentRunningStack[$runningProcessKey]);
                    $this->logger->info('- Removed Process ' . $runningProcessKey . ' from currentRunningStack [' . count($this->processingStack) . ' left in queue]');
                }
            }
        }
    }
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
        $this->processingStack[] = $process;

        $this->logger->debug('Process pushed on to stack. Stack size: '.count($this->processingStack));

        return $this;
    }

    /**
     * @return int
     */
    public function getMaxRunStackSize()
    {
        return $this->maxRunStackSize;
    }

    /**
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
