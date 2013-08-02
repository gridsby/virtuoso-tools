<?php
namespace GridsBy\Virtuoso\Command\Import;


use GridsBy\Virtuoso\BulkLoader;
use GridsBy\Virtuoso\PDOConnection;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Helper\TableHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShowCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('show')
            ->setDescription('Show import tasks')
            ->addOption('all', null, InputOption::VALUE_NONE, 'Show all tasks, not only pending ones');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $connection = new PDOConnection();
        $bulk = new BulkLoader($connection);

        if ($input->getOption('all')) {
            $tasks = $bulk->listTasks();
        } else {
            $tasks = $bulk->listScheduledTasks();
        }

        $output->getFormatter()->setStyle('scheduled', new OutputFormatterStyle());
        $output->getFormatter()->setStyle('active', new OutputFormatterStyle('green'));
        $output->getFormatter()->setStyle('finished', new OutputFormatterStyle('cyan'));
        $output->getFormatter()->setStyle('failed', new OutputFormatterStyle('red'));

        /** @var TableHelper $table */
        $table = $this->getApplication()->getHelperSet()->get('table');
        $table->setHeaders(['Status', 'Graph', 'File']);
        $table->setLayout(TableHelper::LAYOUT_BORDERLESS);

        foreach ($tasks as $task) {
            $table->addRow([$task->status, $task->graph, "<{$task->status}>{$task->file}</{$task->status}>"]);
        }

        $table->render($output);
    }
}
