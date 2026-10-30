<?php

declare(strict_types=1);

namespace App\Billing\UI\Console;

use App\Billing\Application\Exception\InvalidInputException;
use App\Billing\Application\Report\UsageReport;
use App\Billing\Application\Report\UsageReportBuilder;
use App\Billing\Application\Report\UsageReportRow;
use App\Billing\Domain\Shared\Time;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'CSV-отчёт расходов по (user, model)')]
final class BillingReportCommand extends Command
{
    private const HEADER = ['user', 'model', 'tokens_input', 'tokens_output', 'tokens_cache_read', 'tokens_cache_write', 'cost_usd'];

    public function __construct(private readonly UsageReportBuilder $builder)
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
        try {
            $from = $this->option($input, 'from');
            $to = $this->option($input, 'to');
        } catch (InvalidInputException $e) {
            $output->getErrorOutput()->writeln('<error>'.$e->getMessage().'</error>');

            return Command::FAILURE;
        }

        $report = $this->builder->build($from, $to);
        $output->write($this->csv($report));

        return Command::SUCCESS;
    }

    private function option(InputInterface $input, string $name): ?DateTimeImmutable
    {
        $value = $input->getOption($name);
        if ($value === null) {
            return null;
        }
        try {
            return Time::parseIso((string) $value);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidInputException(sprintf('--%s: %s', $name, $e->getMessage()), 0, $e);
        }
    }

    private function csv(UsageReport $report): string
    {
        $lines = [implode(',', self::HEADER)];
        foreach ($report->rows as $row) {
            $lines[] = $this->line($row);
        }
        $lines[] = $this->line($report->total);

        return implode("\n", $lines)."\n";
    }

    private function line(UsageReportRow $row): string
    {
        return implode(',', [
            $row->user,
            $row->model,
            (string) $row->tokensInput,
            (string) $row->tokensOutput,
            (string) $row->tokensCacheRead,
            (string) $row->tokensCacheWrite,
            $row->costUsd,
        ]);
    }
}
