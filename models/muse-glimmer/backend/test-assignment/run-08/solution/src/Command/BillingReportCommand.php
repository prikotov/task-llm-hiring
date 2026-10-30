<?php

declare(strict_types=1);

namespace App\Command;

use Doctrine\DBAL\Connection;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Report billing')]
final class BillingReportCommand extends Command
{
    public function __construct(private Connection $conn)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_OPTIONAL, 'ISO datetime inclusive');
        $this->addOption('to', null, InputOption::VALUE_OPTIONAL, 'ISO datetime exclusive');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from');
        $to = $input->getOption('to');

        $where = 'WHERE cost IS NOT NULL';
        $params = [];
        if ($from) {
            $where .= ' AND ts >= :from';
            $params['from'] = $from;
        }
        if ($to) {
            $where .= ' AND ts < :to';
            $params['to'] = $to;
        }

        $sql = "
            SELECT user_name as user,
                   CONCAT(provider_code, '/', model_code) AS model,
                   SUM(tokens_input) AS tokens_input,
                   SUM(tokens_output) AS tokens_output,
                   SUM(tokens_cache_read) AS tokens_cache_read,
                   SUM(tokens_cache_write) AS tokens_cache_write,
                   SUM(cost) AS cost_usd
            FROM usage_events
            $where
            GROUP BY user_name, provider_code, model_code
            ORDER BY user_name ASC, provider_code ASC, model_code ASC
        ";
        $rows = $this->conn->fetchAllAssociative($sql, $params);

        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');

        $totalTokensInput = '0';
        $totalTokensOutput = '0';
        $totalTokensCacheRead = '0';
        $totalTokensCacheWrite = '0';
        $totalCost = '0';

        foreach ($rows as $r) {
            $cost = number_format((float)$r['cost_usd'], 6, '.', '');
            // ensure 6 decimals
            $output->writeln(sprintf('%s,%s,%s,%s,%s,%s,%s',
                $r['user'],
                $r['model'],
                $r['tokens_input'],
                $r['tokens_output'],
                $r['tokens_cache_read'],
                $r['tokens_cache_write'],
                $cost
            ));
            $totalTokensInput = bcadd($totalTokensInput, (string)$r['tokens_input'], 0);
            $totalTokensOutput = bcadd($totalTokensOutput, (string)$r['tokens_output'], 0);
            $totalTokensCacheRead = bcadd($totalTokensCacheRead, (string)$r['tokens_cache_read'], 0);
            $totalTokensCacheWrite = bcadd($totalTokensCacheWrite, (string)$r['tokens_cache_write'], 0);
            $totalCost = bcadd($totalCost, (string)$r['cost_usd'], 6);
        }

        $totalCostFormatted = number_format((float)$totalCost, 6, '.', '');
        $output->writeln(sprintf('TOTAL,,%s,%s,%s,%s,%s',
            $totalTokensInput,
            $totalTokensOutput,
            $totalTokensCacheRead,
            $totalTokensCacheWrite,
            $totalCostFormatted
        ));

        return Command::SUCCESS;
    }
}
