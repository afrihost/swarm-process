<?php
/**
 * User: sarel
 * Date: 2015/12/30
 * Time: 12:51
 */

use Afrihost\SwarmProcess\SwarmProcess;
use Monolog\Logger;
use Monolog\Processor\MemoryUsageProcessor;
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
            $logger->pushProcessor(new MemoryUsageProcessor());

            $listOfProcessesToRun = array();
            for ($i = 0; $i < 10000; $i++) {
                if (rand(0,5) == 0) {
                    $listOfProcessesToRun[] = 'ls -lahtr; sleep 2';
                } else {
                    $listOfProcessesToRun[] = 'sleep '.rand(4,6);
                }
            }
            $swarm = new SwarmProcess($logger);

            $swarm->setMaxRunStackSize(5);

            foreach ($listOfProcessesToRun as $item) {
                $swarm->pushNativeCommandOnStack($item);
            }

            // Now go run it:
            $swarm->run();
        });

$console->run();
