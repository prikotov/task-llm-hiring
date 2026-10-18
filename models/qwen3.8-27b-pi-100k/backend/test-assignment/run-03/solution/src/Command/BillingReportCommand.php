<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\ReportService;
use App\Shared\ConsoleStreams;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'Отчёт по расходам (CSV) в stdout; unrated-события не попадают в отчёт',
)]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportService $reportService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'начало периода, ISO-8601 UTC (включительно; по умолчанию — вся история)');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'конец периода, ISO-8601 UTC (исключительно; по умолчанию — без ограничения)');
        $this->setHelp(<<<'HELP'
Сводка расходов по событиям, отсортированная парами (user, model).

Пример:
  bin/console app:billing:report --from=2026-09-01T00:00:00Z --to=2026-10-01T00:00:00Z
HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = (string) $input->getOption('from');
        $to = (string) $input->getOption('to');

        try {
            $csv = $this->reportService->buildReport($from === '' ? null : $from, $to === '' ? null : $to);
        } catch (\Throwable $e) {
            ConsoleStreams::stderr($output)->writeln('app:billing:report: '.$e->getMessage());

            return 1;
        }

        $output->write($csv);

        return 0;
    }
}
