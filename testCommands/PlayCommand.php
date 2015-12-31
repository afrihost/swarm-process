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
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

chdir(__DIR__);
require('../vendor/autoload.php');

$console = new Application();

$console
    ->register('play')
    ->setDescription('Play command for testing purposes')
    ->setDefinition(array(
        new InputOption('use-run', null, InputOption::VALUE_NONE, 'Uses the ->run() method of SwarmProcess'),
        new InputOption('use-tick', null, InputOption::VALUE_NONE, 'Uses the ->tick() method of SwarmProcess')
    ))
    ->setCode(
        function (InputInterface $input, OutputInterface $output) {
            switch (true) {
                case ($input->getOption('use-run')):
                    useRun();
                    break;
                case ($input->getOption('use-tick')):
                    useTick();
                    break;
                default:
                    $output->writeln('<error>You should supply either --use-run or --use-tick to choose which way to use the system to test</error>');
            }
        });

$console->run();

function useRun()
{
    $logger = new Logger('swarm_logger');
    $logger->pushProcessor(new MemoryUsageProcessor());

    $listOfProcessesToRun = array();
    for ($i = 0; $i < 20; $i++) {
        $listOfProcessesToRun[] = getCommand();
    }

    $swarm = new SwarmProcess($logger);

    $swarm->setMaxRunStackSize(5);

    foreach ($listOfProcessesToRun as $item) {
        $swarm->pushNativeCommandOnStack($item);
    }

    // Now go run it:
    $swarm->run();
}

function getCommand()
{
//    return 'ls -lahtr';
    return 'sleep '.rand(1,5);
}

function useTick()
{
    $logger = new Logger('swarm_logger');
    $logger->pushProcessor(new MemoryUsageProcessor());

    $swarm = new SwarmProcess($logger);

    $swarm->setMaxRunStackSize(5);

    $counter = 0;
    // Now go run it:
    do {
        // If we have work to give the stack, then let's give it:
        if (++$counter <= 20) {
            $swarm->pushNativeCommandOnStack(getCommand());
        }
    } while ($swarm->tick());
}
