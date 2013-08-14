<?php
namespace GridsBy\Virtuoso\Command\Admin;


use GridsBy\Virtuoso\Administration;
use GridsBy\Virtuoso\PDOConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RolesCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('roles')
            ->addArgument('user', InputArgument::OPTIONAL, 'List only roles granted to this user')
            ->setDescription('List roles');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new PDOConnection();
        $adm = new Administration($connection);

        if ($login = $input->getArgument('user')) {
            $output->writeln('Roles granted to '.$login.':');
            $roles = $adm->grantedRoles($login);
        } else {
            $output->writeln('Roles:');
            $roles = $adm->roleNames();
        }

        if (count($roles) == 0) {
            $output->writeln('  (none)');
        } else {
            foreach ($roles as $role) {
                $output->writeln('  - '.$role);
            }
        }
    }
}
