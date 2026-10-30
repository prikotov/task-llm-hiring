<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Generates billing report in CSV')]
class BillingReportCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
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

        $queryBuilder = $this->entityManager->createQueryBuilder()
            ->select('u')
            ->from(UsageEvent::class, 'u')
            ->where('u.costUsd IS NOT NULL');

        if ($from) {
            $queryBuilder->andWhere('u.ts >= :from')->setParameter('from', $from);
        }
        if ($to) {
            $queryBuilder->andWhere('u.ts < :to')->setParameter('to', $to);
        }

        $events = $queryBuilder->getQuery()->getResult();
        
        $aggregated = [];

        foreach ($events as $event) {
            $user = $event->getUser();
            $model = $event->getModel()->getFullName();
            $key = $user . '|' . $model;

            if (!isset($aggregated[$key])) {
                $aggregated[$key] = [
                    'user' => $user,
                    'model' => $model,
                    'tokens_input' => 0,
                    'tokens_output' => 0,
                    'tokens_cache_read' => 0,
                    'tokens_cache_write' => 0,
                    'cost_usd' => '0',
                ];
            }

            $aggregated[$key]['tokens_input'] += $event->getTokensInput();
            $aggregated[$key]['tokens_output'] += $event->getTokensOutput();
            $aggregated[$key]['tokens_cache_read'] += $event->getTokensCacheRead();
            $aggregated[$key]['tokens_cache_write'] += $event->getTokensCacheWrite();
            $aggregated[$key]['cost_usd'] = bcadd($aggregated[$key]['cost_usd'], $event->getCostUsd(), 6);
        }

        // Sort by user, then model
        uasort($aggregated, function ($a, $b) {
            if ($a['user'] === $b['user']) {
                return strcmp($a['model'], $b['model']);
            }
            return strcmp($a['user'], $b['user']);
        });

        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');

        $totalInput = 0;
        $totalOutput = 0;
        $totalCacheRead = 0;
        $totalCacheWrite = 0;
        $totalCost = '0';

        foreach ($aggregated as $row) {
            $output->writeln(sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $row['user'],
                $row['model'],
                $row['tokens_input'],
                $row['tokens_output'],
                $row['tokens_cache_read'],
                $row['tokens_cache_write'],
                bcadd($row['cost_usd'], '0', 6)
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
            bcadd($totalCost, '0', 6)
        ));

        return Command::SUCCESS;
    }
}
