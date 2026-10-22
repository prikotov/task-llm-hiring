<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'Отчёт расходов (CSV) по пользователям и моделям за период',
)]
final class BillingReportCommand extends Command
{
    public function __construct(
        private readonly ReportService $report,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (включительно), ISO-8601 UTC', '')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (исключительно), ISO-8601 UTC', '');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $csv = $this->report->render((string) $input->getOption('from'), (string) $input->getOption('to'));

        $output->write($csv);

        return Command::SUCCESS;
    }
}
