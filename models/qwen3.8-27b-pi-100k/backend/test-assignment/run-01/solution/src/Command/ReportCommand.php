<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\ReportService;
use App\Support\Iso8601;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'CSV-отчёт расходов по пользователям и моделям (stdout)',
)]
final class ReportCommand extends Command
{
    public function __construct(private readonly ReportService $reportService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_OPTIONAL, 'Начало периода (включительно), ISO-8601 UTC, напр. 2026-09-01T00:00:00Z')
            ->addOption('to', null, InputOption::VALUE_OPTIONAL, 'Конец периода (исключительно), ISO-8601 UTC, напр. 2026-10-01T00:00:00Z');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = $this->parseDateOption($input, 'from');
            $to = $this->parseDateOption($input, 'to');
        } catch (\InvalidArgumentException $e) {
            $output->getErrorOutput()->writeln('Ошибка: '.$e->getMessage());

            return Command::FAILURE;
        }

        if ($from !== null && $to !== null && $from > $to) {
            $output->getErrorOutput()->writeln('Ошибка: --from позже --to');

            return Command::FAILURE;
        }

        try {
            $this->reportService->collect($from, $to);
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка формирования отчёта: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        // Вывод строго в stdout: UTF-8, разделитель ",", перевод строки "\n".
        $output->write(implode("\n", $this->reportService->csvLines())."\n");

        return Command::SUCCESS;
    }

    private function parseDateOption(InputInterface $input, string $name): ?\DateTimeImmutable
    {
        $value = $input->getOption($name);
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Iso8601::parseUtc((string) $value);
        } catch (\InvalidArgumentException $e) {
            throw new \InvalidArgumentException(sprintf('--%s: %s', $name, $e->getMessage()));
        }
    }
}
