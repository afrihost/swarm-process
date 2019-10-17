<?php

use Afrihost\SwarmProcess\Configuration;
use Afrihost\SwarmProcess\SwarmProcess;

class ConfigurationTest extends PHPUnit\Framework\TestCase
{
    /** @var SwarmProcess */
    private $swarm;

    /** @var Configuration */
    private $swarmConfig;

    protected function setUp()
    {
        $this->swarm = new SwarmProcess();
        $this->swarmConfig = new Configuration();
    }

    public function testSetEnforceProcessTimeouts()
    {
        $this->assertFalse($this->swarmConfig->isEnforceProcessTimeouts());

        $this->swarmConfig->setEnforceProcessTimeouts(true);
        $this->assertTrue($this->swarmConfig->isEnforceProcessTimeouts());
    }

    public function testSetCompletedCallback()
    {
        $this->assertNull($this->swarmConfig->getCompletedCallback());

        $this->swarmConfig->setCompletedCallback(function () {
            // do nothing, it's just a callable
        });
        $this->assertIsCallable($this->swarmConfig->getCompletedCallback());
    }

    public function testSetCompletedCallbackMustBeCallable()
    {
        $this->expectException('Exception');

        $this->swarmConfig->setCompletedCallback('thisIsNotACallable');
    }

    public function testSetTickLoopDelayMicroseconds()
    {
        // check default is 0:
        $this->assertEquals(0, $this->swarmConfig->getTickLoopDelayMicroseconds());

        // test set and get:
        $this->swarmConfig->setTickLoopDelayMicroseconds(1234);
        $this->assertEquals(1234, $this->swarmConfig->getTickLoopDelayMicroseconds());
    }
}
