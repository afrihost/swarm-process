<?php
use Afrihost\SwarmProcess\SwarmProcess;
use Psr\Log\NullLogger;

/**
 * User: sarel
 * Date: 2016/01/18
 * Time: 07:56
 */
class SwarmProcessTestNoSetup extends PHPUnit_Framework_TestCase
{
    /**
     * Tests whether if we give it a logger, we get return the same logger if we check which one it has internally
     */
    public function testLoggerGiven()
    {
        $given = new NullLogger();

        $swarm = new SwarmProcess($given);

        $this->assertTrue($given === $swarm->getLogger(), 'Logger given at construction not the same as class has internally');
    }

    public function testLoggerNotGiven()
    {
        $swarm = new SwarmProcess();

        $this->assertInstanceOf('Psr\Log\NullLogger', $swarm->getLogger(), 'Logger expected when none is given, should be the NullLogger');
    }
}
