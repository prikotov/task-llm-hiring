<?php

declare(strict_types=1);

namespace App\Ui\Console;

use App\Billing\Application\CostReport;
use App\Billing\Application\ReportRow;
use App\Shared\UtcTime;
use InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'CSV-отчёт расходов по пользователям и моделям')]
final class ReportCommand extends Command
{
    private const string HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    public function __construct(private readonly CostReport $report)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (ISO-8601, включительно)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (ISO-8601, исключительно)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $err = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        try {
            $from = $input->getOption('from') !== null ? UtcTime::parse((string) $input->getOption('from')) : null;
            $to = $input->getOption('to') !== null ? UtcTime::parse((string) $input->getOption('to')) : null;
        } catch (InvalidArgumentException $e) {
            $err->writeln('ERROR '.$e->getMessage());

            return Command::INVALID;
        }

        $r = $this->report->build($from, $to);
        $out = self::HEADER."\n";
        foreach ([...$r['rows'], $r['total']] as $row) {
            $out .= self::line($row)."\n";
        }
        $output->write($out, false, OutputInterface::OUTPUT_RAW);

        return Command::SUCCESS;
    }

    private static function line(ReportRow $r): string
    {
        return implode(',', array_map(self::csv(...), [
            $r->user, $r->model, $r->tokensInput, $r->tokensOutput, $r->tokensCacheRead, $r->tokensCacheWrite, $r->costUsd,
        ]));
    }

    private static function csv(string $v): string
    {
        return preg_match('/[",\r\n]/', $v) === 1 ? '"'.str_replace('"', '""', $v).'"' : $v;
    }
}
