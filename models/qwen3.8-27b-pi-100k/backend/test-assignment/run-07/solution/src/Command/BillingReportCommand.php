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
    description: 'Отчёт расходов по пользователям и моделям (CSV в stdout).',
)]
final class BillingReportCommand extends Command
{
    private const ISO_UTC_TS = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,6})?Z$/';

    public function __construct(
        private readonly ReportService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода, ISO-8601 UTC, включительно')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода, ISO-8601 UTC, исключительно');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = $this->parseUtcTimestamp((string) $input->getOption('from'), 'from');
            $to = $this->parseUtcTimestamp((string) $input->getOption('to'), 'to');
        } catch (\InvalidArgumentException $e) {
            $output->getErrorOutput()->writeln('<error>' . $e->getMessage() . '</error>');

            return 1;
        }

        $output->write($this->service->buildCsv($from, $to));

        return Command::SUCCESS;
    }

    private function parseUtcTimestamp(?string $value, string $optionName): ?\DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }

        if (preg_match(self::ISO_UTC_TS, $value) !== 1) {
            throw new \InvalidArgumentException(
                "Опция --{$optionName}: ожидается ISO-8601 UTC с суффиксом Z (например 2026-09-01T00:00:00Z)"
            );
        }

        try {
            return new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new \InvalidArgumentException("Опция --{$optionName}: некорректная дата: " . $e->getMessage());
        }
    }
}
