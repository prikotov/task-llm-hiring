<?php

namespace App\Command;

use App\Service\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report')]
class BillingReportCommand extends Command
{
    public function __construct(private ReportService $reportService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_OPTIONAL, 'From date (ISO-8601)')
             ->addOption('to', null, InputOption::VALUE_OPTIONAL, 'To date (ISO-8601)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from');
        $to = $input->getOption('to');

        $data = $this->reportService->generate($from, $to);

        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');

        $totalInput = 0;
        $totalOutput = 0;
        $totalCacheRead = 0;
        $totalCacheWrite = 0;
        $totalCost = '0';

        foreach ($data as $row) {
            $output->writeln(sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $row['user'],
                $row['model'],
                $row['tokens_input'],
                $row['tokens_output'],
                $row['tokens_cache_read'],
                $row['tokens_cache_write'],
                $row['cost_usd']
            ));

            $totalInput += $row['tokens_input'];
            $totalOutput += $row['tokens_output'];
            $totalCacheRead += $row['tokens_cache_read'];
            $totalCacheWrite += $row['tokens_cache_write'];
            $totalCost = bcadd($totalCost, $row['cost_usd'], 6);
        }

        $output->writeln(sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $totalInput,
            $totalOutput,
            $totalCacheRead,
            $totalCacheWrite,
            number_format((float)$totalCost, 6, '.', '')
        ));

        return Command::SUCCESS;
    }
}
