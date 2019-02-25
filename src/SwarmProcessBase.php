<?php
namespace Afrihost\SwarmProcess;

use Psr\Log\LoggerAwareTrait;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

abstract class SwarmProcessBase
{
    use LoggerAwareTrait;

    /** @var $configuration Configuration */
    protected $configuration;

    /**
     * @return Configuration
     */
    public function getConfiguration()
    {
        return $this->configuration;
    }

    /**
     * @param Configuration $configuration
     * @return SwarmProcessBase
     */
    public function setConfiguration($configuration)
    {
        $this->configuration = $configuration;
        return $this;
    }

    /**
     * SwarmProcess constructor.
     *
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger = null, Configuration $configuration = null)
    {
        $this->setLogger($logger ?: new NullLogger());

        $this->logger->debug('__construct(ed) SwarmProcess');

        if (null === $configuration) {
            $configuration = new Configuration();
        }
        $this->configuration = $configuration;
    }
}
