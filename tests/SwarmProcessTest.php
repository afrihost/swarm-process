<?php
use Psr\Log\LoggerInterface;

/**
 * User: sarel
 * Date: 2015/12/29
 * Time: 17:51
 */
class SwarmProcessTest extends PHPUnit_Framework_TestCase
{
    /** @var LoggerInterface */
    private $logger;

    protected function setUp()
    {
        $this->logger = new \Psr\Log\NullLogger();
    }

    public function testClassCreation()
    {
        $swarm = new \Afrihost\SwarmProcess\SwarmProcess($this->logger);

        $this->assertTrue(is_object($swarm), 'Cannot instantiate SwarmProcess class into an object.');
    }
}
