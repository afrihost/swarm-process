<?php
/**
 * Almost exactly like simple-run.php - however this pushes the Process object.
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

$swarmProcess->run();
