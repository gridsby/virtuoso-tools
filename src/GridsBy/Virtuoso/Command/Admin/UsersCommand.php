<?php
namespace GridsBy\Virtuoso\Command\Admin;


use GridsBy\Virtuoso\Administration;
use GridsBy\Virtuoso\PDOConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UsersCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('users')
            ->setDescription('List users');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new PDOConnection();
        $adm = new Administration($connection);

        $users = $adm->userNames();

        $output->writeln('Users:');
        if (count($users) == 0) {
            $output->writeln('  (none)');
        } else {
            foreach ($users as $user) {
                $output->writeln('  - '.$user);
            }
        }
    }
}
