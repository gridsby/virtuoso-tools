<?php
namespace GridsBy\Virtuoso\Command\Admin;


use GridsBy\Virtuoso\Administration;
use GridsBy\Virtuoso\PDOConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class VacuumCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('vacuum')
            ->addArgument('table', InputArgument::OPTIONAL, 'Limit vacuum to specific table')
            ->addArgument('index', InputArgument::OPTIONAL, 'Limit vacuum to specific index of table')
            ->setDescription('Compact adjacent pages holding data that will fit on fewer pages ');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new PDOConnection();
        $adm = new Administration($connection);


        $output->write('Starting vacuum… ');
        $adm->vacuum($input->getArgument('table'), $input->getArgument('index'));

        $output->writeln('done');
    }
}
