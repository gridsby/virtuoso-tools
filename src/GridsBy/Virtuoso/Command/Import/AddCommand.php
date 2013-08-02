<?php
namespace GridsBy\Virtuoso\Command\Import;


use GridsBy\Virtuoso\BulkLoader;
use GridsBy\Virtuoso\PDOConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class AddCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('add')
            ->setDescription('Add new import task')
            ->addArgument('path', InputArgument::REQUIRED, 'Where are the files located?')
            ->addArgument('mask', InputArgument::REQUIRED, 'Which file should be imported? for example: *.nt')
            ->addArgument('graph', InputArgument::REQUIRED, 'Which graph should the files be imported to?')
            ->addOption('recursive', null, InputOption::VALUE_NONE, 'Traverse path recursively');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new PDOConnection();
        $bulk = new BulkLoader($connection);

        $before_tasks = count($bulk->listTasks());

        if ($input->getOption('recursive')) {
            $bulk->addRecursiveTask($input->getArgument('path'), $input->getArgument('mask'), $input->getArgument('graph'));
        } else {
            $bulk->addTask($input->getArgument('path'), $input->getArgument('mask'), $input->getArgument('graph'));
        }

        $after_tasks = count($bulk->listTasks());
        $diff = $after_tasks - $before_tasks;

        $output->writeln("Added {$diff} tasks");
    }
}
