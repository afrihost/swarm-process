<?php

/**
 * User: sarel
 * Date: 2015/12/29
 * Time: 17:51
 */
class SwarmProcessTest extends PHPUnit_Framework_TestCase
{
    public function testClassCreation()
    {
        $swarm = new \Afrihost\SwarmProcess\SwarmProcess();

        $this->assertTrue(is_object($swarm), 'Cannot instantiate SwarmProcess class into an object.');
    }
}
