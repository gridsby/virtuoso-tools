<?php
namespace GridsBy\Virtuoso\Command\Admin;


use GridsBy\Virtuoso\Administration;
use GridsBy\Virtuoso\PDOConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GrantRoleCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('grant-role')
            ->addArgument('user', InputArgument::REQUIRED, 'USER which needs a new role')
            ->addArgument('role', InputArgument::REQUIRED, 'ROLE which needs to be granted to user')
            ->addOption('can-give', null, InputOption::VALUE_NONE, 'If set, user will be able to grant this role to someone else')
            ->setDescription('Grant role to user');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new PDOConnection();
        $adm = new Administration($connection);

        $login = $input->getArgument('user');
        $role = $input->getArgument('role');

        $possible_roles = $adm->roleNames();

        if (!in_array($role, $possible_roles)) {
            $output->writeln('Unknown role: '.$role);
            exit(1);
        }

        $assigned_roles = $adm->grantedRoles($login);

        if (!in_array($role, $assigned_roles)) {
            // otherwise, it is already set
            $adm->grantRole($login, $role, $input->hasOption('can-give'));
        }

        $output->writeln('OK');
    }
}
