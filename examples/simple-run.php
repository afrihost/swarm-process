<?php
/**
 * This examples shows the simplest way of using the SwarmProcess class.
 * The logger is used purely to allow you to see what's happening.
 * If you don't provide the logger, the Psr\Log\NullLogger will be used internally.
 */
use Afrihost\SwarmProcess\SwarmProcess;
use Monolog\Logger;

chdir(__DIR__);
require('../vendor/autoload.php');

$logger = new Logger('swarm_logger');

$swarmProcess = new SwarmProcess($logger);

// Add a few things to do:
$swarmProcess->pushNativeCommandOnQueue('sleep 9');
$swarmProcess->pushNativeCommandOnQueue('sleep 8');
$swarmProcess->pushNativeCommandOnQueue('sleep 7');
$swarmProcess->pushNativeCommandOnQueue('sleep 6');
$swarmProcess->pushNativeCommandOnQueue('sleep 5');
$swarmProcess->pushNativeCommandOnQueue('sleep 5');
$swarmProcess->pushNativeCommandOnQueue('sleep 4');
$swarmProcess->pushNativeCommandOnQueue('sleep 3');
$swarmProcess->pushNativeCommandOnQueue('sleep 2');
$swarmProcess->pushNativeCommandOnQueue('sleep 1');

$swarmProcess->setMaxRunStackSize(4);

$swarmProcess->run();
