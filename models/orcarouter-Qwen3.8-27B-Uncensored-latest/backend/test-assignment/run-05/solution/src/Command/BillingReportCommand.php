<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ReportService;
use App\Support\ImportError;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Отчёт по расходам (CSV в stdout)')]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (включительно, ISO-8601 UTC)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (исключительно, ISO-8601 UTC)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from') !== null ? (string) $input->getOption('from') : null;
        $to = $input->getOption('to') !== null ? (string) $input->getOption('to') : null;

        try {
            $csv = $this->service->buildCsv($from, $to);
        } catch (ImportError $e) {
            $output->getErrorOutput()->writeln($e->getMessage());

            return 1;
        }

        $output->write($csv);

        return 0;
    }
}
