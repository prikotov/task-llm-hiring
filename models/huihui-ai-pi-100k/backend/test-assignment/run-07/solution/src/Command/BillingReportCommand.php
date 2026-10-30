<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\IsoUtc;
use App\DTO\PricesDocument;
use App\Service\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'Отчёт по расходам (CSV) за период --from (включительно) .. --to (исключительно)',
)]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportService $reportService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (ISO-8601 UTC, включительно)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (ISO-8601 UTC, исключительно)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = $this->parseOption((string) $input->getOption('from'));
            $to = $this->parseOption((string) $input->getOption('to'));
        } catch (\ValueError $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $rows = $this->reportService->build($from, $to);

        $totalTokens = [0, 0, 0, 0];
        $totalCost = '0';

        $lines = ['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd'];
        foreach ($rows as $row) {
            $totalTokens[0] += $row->tokensInput();
            $totalTokens[1] += $row->tokensOutput();
            $totalTokens[2] += $row->tokensCacheRead();
            $totalTokens[3] += $row->tokensCacheWrite();
            $totalCost = \App\Domain\Money::add($totalCost, $row->costUsd);

            $lines[] = implode(',', [
                $row->user,
                $row->model,
                (string) $row->tokensInput(),
                (string) $row->tokensOutput(),
                (string) $row->tokensCacheRead(),
                (string) $row->tokensCacheWrite(),
                $row->costUsdFormatted(),
            ]);
        }

        $lines[] = implode(
            ',',
            [
                'TOTAL',
                '',
                (string) $totalTokens[0],
                (string) $totalTokens[1],
                (string) $totalTokens[2],
                (string) $totalTokens[3],
                \App\Domain\Money::to6($totalCost),
            ]
        );

        $stream = $output;
        foreach ($lines as $line) {
            $stream->write($line . "\n");
        }

        return Command::SUCCESS;
    }

    /**
     * null -> без ограничения по периоду.
     */
    private function parseOption(string $value): ?\DateTimeImmutable
    {
        if ($value === '' || $value === null) {
            return null;
        }

        return IsoUtc::parse($value);
    }
}
