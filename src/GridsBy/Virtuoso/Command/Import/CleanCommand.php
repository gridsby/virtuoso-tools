<?php
namespace GridsBy\Virtuoso\Command\Import;


use GridsBy\Virtuoso\BulkLoader;
use GridsBy\Virtuoso\PDOConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

class CleanCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('clean')
            ->setDescription('Cleanup import tasks');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new PDOConnection();
        $bulk = new BulkLoader($connection);

        $before_tasks = count($bulk->listTasks());

        if ($input->isInteractive()) {
            /** @var DialogHelper $dialog */
            $dialog = $this->getApplication()->getHelperSet()->get('dialog');
            $should_do = $dialog->askConfirmation($output, 'Are you sure that you want to clean the import queue?', false);
        } else {
            $should_do = true;
        }

        if ($should_do) {
            $bulk->cleanTasks();
            $output->writeln("Removed {$before_tasks} tasks");
        }
    }
}
