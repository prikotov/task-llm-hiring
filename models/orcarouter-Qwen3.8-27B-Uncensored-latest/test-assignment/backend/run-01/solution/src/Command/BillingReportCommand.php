<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\ReportService;
use App\Domain\Billing\Exception\BillingException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Отчёт расходов (CSV) по рассчитанным событиям.')]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportService $reportService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (включительно), ISO-8601 UTC')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (исключительно), ISO-8601 UTC');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = $input->getOption('from');
            $to = $input->getOption('to');
            $rows = $this->reportService->report(
                $from !== null && $from !== '' ? (string) $from : null,
                $to !== null && $to !== '' ? (string) $to : null,
            );
        } catch (BillingException $e) {
            $output->getErrorOutput()->writeln($e->getMessage());

            return Command::FAILURE;
        }

        $stream = \fopen('php://stdout', 'w');
        if ($stream === false) {
            return Command::FAILURE;
        }
        foreach ($rows as $row) {
            \fputs($stream, \implode(',', $row)."\n");
        }
        \fflush($stream);
        \fclose($stream);

        return Command::SUCCESS;
    }
}
