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

    public function testSetMaxRunStack()
    {
        $swarm = new \Afrihost\SwarmProcess\SwarmProcess($this->logger);

        $this->assertEquals($swarm->getMaxRunStackSize(), 10); // default is 10

        $fluent = $swarm->setMaxRunStackSize(20);
        $this->assertEquals($swarm->getMaxRunStackSize(), 20);

        $this->assertInstanceOf('\Afrihost\SwarmProcess\SwarmProcess', $fluent);
    }

    public function testClassCreation()
    {
        $swarm = new \Afrihost\SwarmProcess\SwarmProcess($this->logger);

        $this->assertTrue(is_object($swarm), 'Cannot instantiate SwarmProcess class into an object.');
    }

    protected function setUp()
    {
        $this->logger = new \Psr\Log\NullLogger();
    }


}
