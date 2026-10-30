<?php

declare(strict_types=1);

namespace App\Billing\Command;

use App\Billing\Service\ReportService;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'CSV-отчёт по расчёту расходов (в stdout), [--from=ISO --to=ISO]',
)]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportService $report)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода, ISO-8601 UTC (включительно)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода, ISO-8601 UTC (исключительно)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $this->parseInstant((string) $input->getOption('from'), 'from', $output);
        if ($from === false) {
            return 1;
        }
        $to = $this->parseInstant((string) $input->getOption('to'), 'to', $output);
        if ($to === false) {
            return 1;
        }

        $output->write($this->report->build($from, $to));

        return 0;
    }

    /** @return DateTimeImmutable|false|null */
    private function parseInstant(string $value, string $optionName, OutputInterface $output): DateTimeImmutable|false|null
    {
        $value = trim($value);
        if ($value === '') {
            return null;
        }
        try {
            return new DateTimeImmutable($value, new DateTimeZone('UTC'))->setTimezone(new DateTimeZone('UTC'));
        } catch (\Exception $e) {
            $output->getErrorOutput()->writeln(sprintf('<error>Некорректное значение --%s: "%s".</error>', $optionName, $value));

            return false;
        }
    }
}
