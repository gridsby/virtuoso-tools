<?php
namespace GridsBy\Virtuoso\Command\Admin;


use GridsBy\Virtuoso\Administration;
use GridsBy\Virtuoso\PDOConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Helper\DialogHelper;

class KillCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('kill')
            ->addArgument('code', InputArgument::OPTIONAL, 'What code should transactions return to their callers?', Administration::ERR_ROLLBACK_AFTER_SQL_ERROR)
            ->setDescription('Kill all active transactions');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new PDOConnection();
        $adm = new Administration($connection);

        if ($input->isInteractive()) {
            /** @var DialogHelper $dialog */
            $dialog = $this->getApplication()->getHelperSet()->get('dialog');
            $should_do = $dialog->askConfirmation($output, 'Are you sure that you want to kill all transactions?', false);
        } else {
            $should_do = true;
        }

        if ($should_do) {
            $output->write('Killing transactions… ');
            $adm->killAll($input->getArgument('code'));
            $output->writeln('done');
        }
    }
}
