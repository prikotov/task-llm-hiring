<?php

namespace App\Command;

use App\Service\ReportingService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Generates a billing report in CSV format')]
class BillingReportCommand extends Command
{
    public function __construct(private ReportingService $reportingService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Start date (ISO-8601)');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'End date (ISO-8601)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from') ? new \DateTimeImmutable($input->getOption('from')) : null;
        $to = $input->getOption('to') ? new \DateTimeImmutable($input->getOption('to')) : null;

        $rows = $this->reportingService->generateReport($from, $to);

        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');

        $grandTotalInput = 0;
        $grandTotalOutput = 0;
        $grandTotalCacheRead = 0;
        $grandTotalCacheWrite = 0;
        $grandTotalCost = '0';

        foreach ($rows as $row) {
            $user = $row['user'];
            $model = $row['provider'] . '/' . $row['modelCode'];
            $inputTokens = (int) $row['totalInput'];
            $outputTokens = (int) $row['totalOutput'];
            $cacheReadTokens = (int) $row['totalCacheRead'];
            $cacheWriteTokens = (int) $row['totalCacheWrite'];
            $costUsd = $row['totalCost'];

            $output->writeln(sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $user,
                $model,
                $inputTokens,
                $outputTokens,
                $cacheReadTokens,
                $cacheWriteTokens,
                $this->formatCost($costUsd)
            ));

            $grandTotalInput += $inputTokens;
            $grandTotalOutput += $outputTokens;
            $grandTotalCacheRead += $cacheReadTokens;
            $grandTotalCacheWrite += $cacheWriteTokens;
            $grandTotalCost = bcadd($grandTotalCost, $costUsd, 18);
        }

        $output->writeln(sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $grandTotalInput,
            $grandTotalOutput,
            $grandTotalCacheRead,
            $grandTotalCacheWrite,
            $this->formatCost($grandTotalCost)
        ));

        return Command::SUCCESS;
    }

    private function formatCost(string $cost): string
    {
        // The task says "cost_usd — сумма стоимостей событий группы, ровно 6 знаков после точки"
        // and "roound ... to 6 decimal places".
        // However, for the total, it should also be 6 digits.
        // The requirement for TOTAL is "sum of costs".
        // Let's use the same rounding.
        
        // Since grandTotalCost is calculated by summing, it might have more precision.
        // But the task says: "Суммы отчёта — сумма уже округлённых стоимостей событий группы, без повторного округления слагаемых."
        // This is tricky. The rows are already rounded.
        // Wait, if I sum the rounded costs from the rows, it is the same as summing unrounded costs? No.
        // "Суммы отчёта — сумма уже округлённых стоимостей событий группы, без повторного округления слагаемых."
        // This means for each group, I have a rounded cost. The TOTAL is the sum of these rounded costs.
        
        // Re-reading: "cost_usd — сумма стоимостей событий группы, ровно 6 знаков после точки"
        // "Суммы отчёта — сумма уже округлённых стоимостей событий группы, без повторного округления слагаемых."
        
        // So, for each group, I should calculate the sum of rounded event costs.
        // My current query `SUM(u.costUsd)` might not be doing that if `costUsd` is already rounded.
        // Yes, it should work if `u.costUsd` stores the rounded cost.

        // Let's check the rounding for the group. 
        // If I want to be strictly follow "sum of already rounded costs", 
        // then the query `SUM(u.costUsd)` is perfect if `u.cost_usd` contains the rounded value.

        // Wait, I have a potential issue. The $grandTotalCost calculation in my loop:
        // $grandTotalCost = bcadd($grandTotalCost, $costUsd, 18);
        // And $costUsd comes from $row['totalCost'].
        // If $row['totalCost'] is the sum of rounded costs, it's fine.

        return $this->bcRoundHalfUp($cost, 6);
    }
    
    private function bcRoundHalfUp(string $number, int $precision): string
    {
        if (strpos($number, '.') === false) {
            return bcadd($number, '0', $precision);
        }

        $add = '0.' . str_repeat('0', $precision) . '5';
        return bcadd($number, $add, $precision);
    }
}
