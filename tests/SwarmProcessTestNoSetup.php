<?php
use Afrihost\SwarmProcess\SwarmProcess;
use Psr\Log\AbstractLogger;
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
        $given = new TestLogger();

        $swarm = new SwarmProcess($given);

        $this->assertSame($given, \PHPUnit_Framework_Assert::getObjectAttribute($swarm, 'logger'), 'Logger given at construction not the same as class has internally');
    }

    public function testLoggerNotGiven()
    {
        $swarm = new SwarmProcess();

        $this->assertInstanceOf('Psr\Log\NullLogger', \PHPUnit_Framework_Assert::getObjectAttribute($swarm, 'logger'), 'Logger expected when none is given, should be the NullLogger');
    }
}

class TestLogger extends AbstractLogger
{
    /**
     * {@inheritdoc}
     */
    public function log($level, $message, array $context = []) {}
}

