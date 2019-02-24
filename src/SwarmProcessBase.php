<?php
namespace Afrihost\SwarmProcess;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

abstract class SwarmProcessBase
{
    use LoggerAwareTrait;

    /**
     * SwarmProcess constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        $this->setLogger($logger ?: new NullLogger());

        $this->logger->debug('__construct(ed) SwarmProcess');
    }
}
