<?php

declare(strict_types=1);

namespace App\Command;

use App\Report\ReportCsv;
use App\Report\ReportService;
use App\Util\DateTimeUtc;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'CSV-отчёт расходов по пользователям и моделям',
)]
final class BillingReportCommand extends Command
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {
        parent::__construct();
    }

    #[\Override]
    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_OPTIONAL, 'Начало периода, включительно (ISO-8601 UTC, например 2026-09-01T00:00:00Z)')
            ->addOption('to', null, InputOption::VALUE_OPTIONAL, 'Конец периода, исключительно (ISO-8601 UTC, например 2026-10-01T00:00:00Z)');
    }

    #[\Override]
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $errorOut = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        try {
            $from = $this->parseBound((string) $input->getOption('from'));
            $to = $this->parseBound((string) $input->getOption('to'));

            $report = $this->reportService->report($from, $to);

            // Отчёт — строго в stdout.
            $output->write(ReportCsv::render($report['groups'], $report['total']));

            return Command::SUCCESS;
        } catch (\Throwable $e) {
            $errorOut->writeln('Ошибка формирования отчёта: ' . $e->getMessage());

            return Command::FAILURE;
        }
    }

    private function parseBound(string $value): ?\DateTimeImmutable
    {
        if ($value === '') {
            return null;
        }

        return DateTimeUtc::parse($value);
    }
}
