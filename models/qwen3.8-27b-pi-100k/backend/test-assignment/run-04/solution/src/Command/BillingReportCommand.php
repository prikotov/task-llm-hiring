<?php

declare(strict_types=1);

namespace App\Command;

use App\Exception\ImportException;
use App\Service\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'CSV-отчёт по расходам: stdout или файл --out')]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportService $reportService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (ISO-8601 UTC), включительно')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (ISO-8601 UTC), исключительно')
            ->addOption('out', null, InputOption::VALUE_REQUIRED, 'Файл для записи CSV (по умолчанию — stdout)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from');
        $to = $input->getOption('to');
        $out = $input->getOption('out');

        try {
            $csv = $this->reportService->build(\is_string($from) ? $from : null, \is_string($to) ? $to : null);
        } catch (ImportException $e) {
            $output->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        if (!\is_string($out) || $out === '') {
            // CSV — в stdout, без лишней консольной обёртки
            \file_put_contents('php://stdout', $csv);

            return Command::SUCCESS;
        }

        $bytes = \file_put_contents($out, $csv);
        if ($bytes === false) {
            $output->writeln('Не удалось записать файл: ' . $out);

            return Command::FAILURE;
        }

        $output->writeln(sprintf('Отчёт записан: %s (%d байт)', $out, $bytes));

        return Command::SUCCESS;
    }
}
