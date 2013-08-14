<?php
namespace GridsBy\Virtuoso\Command\Admin;


use GridsBy\Virtuoso\Administration;
use GridsBy\Virtuoso\PDOConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class RevokeRoleCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('revoke-role')
            ->addArgument('user', InputArgument::REQUIRED, 'USER which does not need a role anymore')
            ->addArgument('role', InputArgument::REQUIRED, 'ROLE which needs to be revoked from user')
            ->setDescription('Revoke role from user');
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

        if (in_array($role, $assigned_roles)) {
            // otherwise, it is already revoked
            $adm->revokeRole($login, $role);
        }

        $output->writeln('OK');
    }
}
