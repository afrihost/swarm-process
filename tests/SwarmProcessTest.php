<?php
use Afrihost\SwarmProcess\SwarmProcess;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * User: sarel
 * Date: 2015/12/29
 * Time: 17:51
 */
class SwarmProcessTest extends PHPUnit_Framework_TestCase
{
    /** @var LoggerInterface */
    private $logger;

    /** @var SwarmProcess */
    private $swarm;

    protected function setUp() {
        $this->logger = new NullLogger();
        $this->swarm = new SwarmProcess($this->logger);
    }

    public function testSetMaxRunStack()
    {
        $this->assertEquals($this->swarm->getMaxRunStackSize(), 10); // default is 10

        $fluent = $this->swarm->setMaxRunStackSize(20);
        $this->assertEquals($this->swarm->getMaxRunStackSize(), 20);

        $this->assertInstanceOf('\Afrihost\SwarmProcess\SwarmProcess', $fluent);
    }

    public function testClassCreation()
    {
        $this->assertTrue(is_object($this->swarm), 'Cannot instantiate SwarmProcess class into an object.');
    }

    /**
     * @expectedException \OutOfBoundsException
     */
    public function testSetMaxRunStackSizeThrowsOutOfBounds()
    {
        $this->swarm->setMaxRunStackSize(-1);
    }

}
