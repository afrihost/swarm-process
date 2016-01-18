<?php
/**
 * The below illustrates how you can provide closure callbacks to the run method (you can provide either or both)
 * in order to have more (or the same) control over the queue and when it ends - while SwarmProcess is running
 * the jobs you gave it to run.
 *
 * This examples shows the simplest way of using the SwarmProcess class.
 * The logger is used purely to allow you to see what's happening.
 * If you don't provide the logger, the Psr\Log\NullLogger will be used internally.
 */
use Afrihost\SwarmProcess\SwarmProcess;
use Monolog\Logger;
use Symfony\Component\Process\Process;

chdir(__DIR__);
require('../vendor/autoload.php');

$logger = new Logger('swarm_logger');

$swarmProcess = new SwarmProcess($logger);

// Add a few things to do:
$swarmProcess->pushProcessOnQueue(new Process('sleep 9'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 8'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 7'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 6'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 5'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 5'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 4'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 3'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 2'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 1'));

$swarmProcess->setMaxRunStackSize(4);

/**
 * @return null|Process
 */
$closureAddMoreStuff = function () {
    if (rand(0, 1000) == 0) { // just some randomization to illustrate the point of real world
        return new Process('sleep 5');
    }

    return null;
};

$swarmProcess->run(
    $closureAddMoreStuff,
    function () {
        return true; // this could be any range of logic, a DB call, anything you want
    }
);
