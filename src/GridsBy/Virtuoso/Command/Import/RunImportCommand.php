<?php
namespace GridsBy\Virtuoso\Command\Import;


use GridsBy\Virtuoso\BulkLoader;
use GridsBy\Virtuoso\PDOConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RunImportCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('run-import')
            ->setDescription('Run import thread')
            ->addOption('limit', null, InputOption::VALUE_OPTIONAL, 'Stop after importing number of batches (batch consists of N [up to 100] files, where N-1 files total size is less than 2Mb)');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new PDOConnection();
        $bulk = new BulkLoader($connection);

        $limit = $input->getOption('limit');

        if ($limit) {
            $output->writeln("Starting import (will stop after {$limit} batches)…");
        } else {
            $output->writeln('Starting import…');
        }

        $bulk->runTasks($limit);

        $output->writeln("Done!");
    }
}
