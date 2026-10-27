<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ReportService;
use App\Shared\InputValidator;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'CSV-отчёт расходов по пользователям и моделям (в stdout)')]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportService $reportService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (включительно), ISO-8601 UTC')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (исключительно), ISO-8601 UTC');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = $input->getOption('from') !== null ? InputValidator::utcDateTime((string) $input->getOption('from'), 'report --from') : null;
            $to = $input->getOption('to') !== null ? InputValidator::utcDateTime((string) $input->getOption('to'), 'report --to') : null;
        } catch (\App\Shared\ImportException $e) {
            $output->getErrorOutput()->writeln('<error>'.$e->getMessage().'</error>');

            return 1;
        }

        $stream = fopen('php://stdout', 'w');
        foreach ($this->reportService->build($from, $to) as $row) {
            fwrite($stream, implode(',', $row)."\n");
        }
        fclose($stream);

        return 0;
    }
}
