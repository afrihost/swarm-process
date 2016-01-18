<?php
/**
 * This example shows you how to start running the SwarmProcess and add work for it to do while it's busy running the jobs.
 *
 * It merely runs the same basic code the ->run() function uses internally - but this allows you to add more work before it
 * checks whether the tick() returns true, meaning there's still work busy processing (or work in the queue).
 *
 * ->tick() returns false if:
 * 1) The queue is empty AND
 * 2) The work stack is empty
 *
 * You can, off course, add an "|| true" to make it a never-ending loop.
 *
 * It is important, though, to protect your loop by wrapping your own code in a try-catch
 *
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

// Build a list of things we'll be adding later on
$add_these = array();
$add_these[] = new Process('sleep 9');
$add_these[] = new Process('sleep 8');
$add_these[] = new Process('sleep 7');
$add_these[] = new Process('sleep 6');
$add_these[] = new Process('sleep 5');
$add_these[] = new Process('sleep 5');
$add_these[] = new Process('sleep 4');
$add_these[] = new Process('sleep 3');
$add_these[] = new Process('sleep 2');
$add_these[] = new Process('sleep 1');

$swarmProcess->setMaxRunStackSize(4);

do {
    try {
        if (count($add_these) > 0) {
            $swarmProcess->pushProcessOnQueue(array_shift($add_these));
        }
    } catch (Exception $e) {
        // do something intelligent with the exception - but do not let the loop end, you will lose work
    }
} while ($swarmProcess->tick());
