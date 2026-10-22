<?php

declare(strict_types=1);

namespace App\Infrastructure\Console;

use App\Application\Billing\ReportService;
use DateTimeImmutable;
use DateTimeZone;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

#[AsCommand(name: 'app:billing:report', description: 'Формирует CSV-отчёт о расходах')]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportService $reportService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода включительно (ISO-8601)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода исключительно (ISO-8601)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = $this->parseDateOption($input->getOption('from'), '--from');
            $to = $this->parseDateOption($input->getOption('to'), '--to');
        } catch (\InvalidArgumentException $exception) {
            $this->error($output, $exception->getMessage());

            return Command::FAILURE;
        }

        if ($from !== null && $to !== null && $from > $to) {
            $this->error($output, '--from must be earlier than or equal to --to.');

            return Command::FAILURE;
        }

        $output->write($this->reportService->generate($from, $to), false, OutputInterface::OUTPUT_RAW);

        return Command::SUCCESS;
    }

    private function parseDateOption(mixed $value, string $option): ?DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }
        if (!is_string($value) || $value === '') {
            throw new \InvalidArgumentException(sprintf('%s must be an ISO-8601 date.', $option));
        }

        try {
            return (new DateTimeImmutable($value, new DateTimeZone('UTC')))
                ->setTimezone(new DateTimeZone('UTC'));
        } catch (Throwable $exception) {
            throw new \InvalidArgumentException(sprintf('%s has invalid date "%s".', $option, $value), 0, $exception);
        }
    }

    private function error(OutputInterface $output, string $message): void
    {
        $target = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        $target->writeln('ERROR: '.$message);
    }
}
