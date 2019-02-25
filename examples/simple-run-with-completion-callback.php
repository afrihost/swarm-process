<?php
/**
 * The below illustrates how you can provide closure callbacks to the run a method on each completion of a process/job.
 * The idea is to provide a way to make decisions on the result of the job, when it is done.
 *
 * I order to achieve this configuration, a Configuration object is passed in.
 *
 * This examples shows the simplest way of using the SwarmProcess class.
 * The logger is used purely to allow you to see what's happening.
 * If you don't provide the logger, the Psr\Log\NullLogger will be used internally.
 */

use Afrihost\SwarmProcess\Configuration;
use Afrihost\SwarmProcess\SwarmProcess;
use Monolog\Logger;
use Symfony\Component\Process\Process;

chdir(__DIR__);
require('../vendor/autoload.php');

$logger = new Logger('swarm_logger');

$closure = function(Process $process) use ($logger) {
    $logger->warning('We\'re checking the exit code of a process, and it was: '.$process->getExitCode().' ['.$process->getExitCodeText().']');
};

$swarmProcess = new SwarmProcess($logger, (new Configuration())->setCompletedCallback($closure));

// Add a few things to do:
$swarmProcess->pushProcessOnQueue(new Process('sleep 9'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 8'));
$swarmProcess->pushProcessOnQueue(new Process('sleep')); // should cause an error, thus non-0 exit code
$swarmProcess->pushProcessOnQueue(new Process('sleep 6'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 5'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 5'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 4'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 3'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 2'));
$swarmProcess->pushProcessOnQueue(new Process('sleep 1'));

$swarmProcess->setMaxRunStackSize(4);

$swarmProcess->run();
