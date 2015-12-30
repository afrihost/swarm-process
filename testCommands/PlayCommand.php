<?php
/**
 * User: sarel
 * Date: 2015/12/30
 * Time: 12:51
 */

use Afrihost\SwarmProcess\SwarmProcess;
use Monolog\Logger;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\VarDumper\VarDumper;

chdir(__DIR__);
require('../vendor/autoload.php');

$console = new Application();

$console
    ->register('play')
    ->setDescription('Play command for testing purposes')
    ->setCode(
        function (InputInterface $input, OutputInterface $output) {
            $logger = new Logger('swarm_logger');

            $swarm = new SwarmProcess($logger);

            $swarm->setMaxRunStackSize(5);
        });

$console->run();
