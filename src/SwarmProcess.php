<?php
/**
 * User: sarel
 * Date: 2015/12/29
 * Time: 17:48
 */

namespace Afrihost\SwarmProcess;

use Psr\Log\LoggerInterface;

class SwarmProcess
{
    /** @var LoggerInterface */
    protected $logger;

    /** @var int */
    protected $maxRunStackSize = 10;

    /** @var array */
    protected $processingStack = array();

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
