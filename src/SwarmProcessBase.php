<?php
/**
 * User: sarel
 * Date: 2016/01/18
 * Time: 08:16
 */

namespace Afrihost\SwarmProcess;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

abstract class SwarmProcessBase
{
    /** @var LoggerInterface */
    protected $logger;

    /**
     * SwarmProcess constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null)
    {
        if (!is_null($logger)) {
            $this->logger = $logger;
        } else {
            $this->logger = new NullLogger();
        }

        $this->getLogger()->debug('__construct(ed) SwarmProcess');
    }

}
