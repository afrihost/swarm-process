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

    /**
     * SwarmProcess constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}
