<?php

namespace App\Command;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Generate billing report CSV')]
class BillingReportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_OPTIONAL, 'Start ISO8601 inclusive');
        $this->addOption('to', null, InputOption::VALUE_OPTIONAL, 'End ISO8601 exclusive');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fromOption = $input->getOption('from');
        $toOption = $input->getOption('to');

        $from = $fromOption ? new \DateTimeImmutable($fromOption) : null;
        $to = $toOption ? new \DateTimeImmutable($toOption) : null;

        $qb = $this->em->createQueryBuilder();
        $qb->select(
                'e.user AS user',
                'e.providerCode AS providerCode',
                'e.modelCode AS modelCode',
                'SUM(e.tokensInput) AS tokens_input',
                'SUM(e.tokensOutput) AS tokens_output',
                'SUM(e.tokensCacheRead) AS tokens_cache_read',
                'SUM(e.tokensCacheWrite) AS tokens_cache_write',
                'SUM(e.costUsd) AS cost_usd'
            )
            ->from(UsageEvent::class, 'e')
            ->where('e.costUsd IS NOT NULL');

        if ($from) {
            $qb->andWhere('e.ts >= :from')
               ->setParameter('from', $from);
        }
        if ($to) {
            $qb->andWhere('e.ts < :to')
               ->setParameter('to', $to);
        }

        $qb->groupBy('e.user')
           ->addGroupBy('e.providerCode')
           ->addGroupBy('e.modelCode')
           ->orderBy('e.user', 'ASC')
           ->addOrderBy('e.providerCode', 'ASC')
           ->addOrderBy('e.modelCode', 'ASC');

        $rows = $qb->getQuery()->getResult();

        // Output CSV header
        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');

        $totalInput = 0;
        $totalOutput = 0;
        $totalCacheRead = 0;
        $totalCacheWrite = 0;
        $totalCost = '0';

        foreach ($rows as $row) {
            $user = $row['user'];
            $model = $row['providerCode'] . '/' . $row['modelCode'];
            $tokensInput = (string)$row['tokens_input'];
            $tokensOutput = (string)$row['tokens_output'];
            $tokensCacheRead = (string)$row['tokens_cache_read'];
            $tokensCacheWrite = (string)$row['tokens_cache_write'];
            $costUsd = $row['cost_usd'];

            // ensure cost has 6 decimals
            if (strpos($costUsd, '.') === false) {
                $costUsd .= '.000000';
            } else {
                $parts = explode('.', $costUsd);
                $dec = str_pad($parts[1], 6, '0', STR_PAD_RIGHT);
                $dec = substr($dec, 0, 6);
                $costUsd = $parts[0] . '.' . $dec;
            }

            $output->writeln(sprintf('%s,%s,%s,%s,%s,%s,%s', $user, $model, $tokensInput, $tokensOutput, $tokensCacheRead, $tokensCacheWrite, $costUsd));

            $totalInput += (int)$tokensInput;
            $totalOutput += (int)$tokensOutput;
            $totalCacheRead += (int)$tokensCacheRead;
            $totalCacheWrite += (int)$tokensCacheWrite;
            $totalCost = bcadd($totalCost, $costUsd, 6);
        }

        // TOTAL line
        $totalCostFormatted = number_format((float)$totalCost, 6, '.', '');
        // But using bc for formatting to avoid float? We'll format via bc.
        // Ensure 6 decimals
        if (strpos($totalCost, '.') === false) {
            $totalCostFormatted = $totalCost . '.000000';
        } else {
            [$i, $d] = explode('.', $totalCost);
            $d = str_pad($d, 6, '0', STR_PAD_RIGHT);
            $d = substr($d, 0, 6);
            $totalCostFormatted = $i . '.' . $d;
        }

        $output->writeln(sprintf('TOTAL,,%d,%d,%d,%d,%s', $totalInput, $totalOutput, $totalCacheRead, $totalCacheWrite, $totalCostFormatted));

        return Command::SUCCESS;
    }
}
