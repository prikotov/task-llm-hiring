<?php

declare(strict_types=1);

namespace App\Command;

use App\Billing\ReportService;
use App\Support\DateTimeParser;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * app:billing:report [--from=ISO] [--to=ISO]
 *
 * CSV в stdout; сервисные сообщения — в stderr. Период [from, to) — по ts
 * событий (from включительно, to исключительно).
 */
#[AsCommand(
    name: 'app:billing:report',
    description: 'Отчёт по расходам (CSV: user, model, токены, cost_usd, TOTAL)',
)]
final class BillingReportCommand extends Command
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_OPTIONAL, 'Начало периода (включительно), ISO-8601 UTC', null)
            ->addOption('to', null, InputOption::VALUE_OPTIONAL, 'Конец периода (исключительно), ISO-8601 UTC', null);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = $this->parseOption($input, 'from');
            $to = $this->parseOption($input, 'to');
        } catch (\DomainException $e) {
            $output->getErrorOutput()->writeln('ОШИБКА АРГУМЕНТОВ: '.$e->getMessage());

            return Command::FAILURE;
        }

        $csv = $this->reportService->buildReport($from, $to);
        // Вывод строго в stdout, байт-в-байт (CSV уже заканчивается "\n").
        $output->write($csv);

        return Command::SUCCESS;
    }

    private function parseOption(InputInterface $input, string $name): ?\DateTimeImmutable
    {
        $value = $input->getOption($name);
        if ($value === null || $value === '') {
            return null;
        }

        return DateTimeParser::parseUtc("--$name", (string) $value);
    }
}
