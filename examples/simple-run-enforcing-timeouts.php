<?php
/**
 * The below illustrates how you can enforce the timeouts. Previously, SwarmProcess ran and never checked/enforced timeouts.
 * Now it does, but it is (for the sake of backward compatibility) an optional behaviour that is off by default.
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

$configuration = (new Configuration())
    ->setEnforceProcessTimeouts(true);

$swarmProcess = new SwarmProcess($logger, $configuration);

// Add a few things to do:
$swarmProcess->pushProcessOnQueue(new Process('sleep 9', null, null, null, 5));
$swarmProcess->pushProcessOnQueue(new Process('sleep 8', null, null, null, 5));
$swarmProcess->pushProcessOnQueue(new Process('sleep 7', null, null, null, 5));
$swarmProcess->pushProcessOnQueue(new Process('sleep 6', null, null, null, 5));
$swarmProcess->pushProcessOnQueue(new Process('sleep 5', null, null, null, 5));
$swarmProcess->pushProcessOnQueue(new Process('sleep 4', null, null, null, 5));
$swarmProcess->pushProcessOnQueue(new Process('sleep 3', null, null, null, 5));
$swarmProcess->pushProcessOnQueue(new Process('sleep 2', null, null, null, 5));
$swarmProcess->pushProcessOnQueue(new Process('sleep 1', null, null, null, 5));

$swarmProcess->setMaxRunStackSize(4);

$swarmProcess->run();
