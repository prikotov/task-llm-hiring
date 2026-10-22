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

#[AsCommand(
    name: 'app:billing:report',
    description: 'Генерация CSV-отчёта по тарифицированным событиям'
)]
final class BillingReportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (ISO-8601, включительно)');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (ISO-8601, исключительно)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.costUsd IS NOT NULL');

        $from = $input->getOption('from');
        $to = $input->getOption('to');

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', new \DateTimeImmutable($from));
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', new \DateTimeImmutable($to));
        }

        $events = $qb->getQuery()->getResult();

        $rows = [];
        foreach ($events as $event) {
            $key = $event->getUser() . '|' . $event->getProvider()->getCode() . '/' . $event->getModel()->getCode();
            if (!isset($rows[$key])) {
                $rows[$key] = [
                    'user' => $event->getUser(),
                    'model' => $event->getProvider()->getCode() . '/' . $event->getModel()->getCode(),
                    'tokens_input' => 0,
                    'tokens_output' => 0,
                    'tokens_cache_read' => 0,
                    'tokens_cache_write' => 0,
                    'cost_usd' => '0',
                ];
            }
            $rows[$key]['tokens_input'] += $event->getTokensInput();
            $rows[$key]['tokens_output'] += $event->getTokensOutput();
            $rows[$key]['tokens_cache_read'] += $event->getTokensCacheRead();
            $rows[$key]['tokens_cache_write'] += $event->getTokensCacheWrite();
            $rows[$key]['cost_usd'] = bcadd($rows[$key]['cost_usd'], $event->getCostUsd(), 6);
        }

        // Сортировка по user, затем по model
        uasort($rows, function ($a, $b) {
            if ($a['user'] === $b['user']) {
                return strcmp($a['model'], $b['model']);
            }
            return strcmp($a['user'], $b['user']);
        });

        $out = fopen('php://output', 'w');
        fputcsv($out, ['user','model','tokens_input','tokens_output','tokens_cache_read','tokens_cache_write','cost_usd'], ',', '"', '\\');
        foreach ($rows as $row) {
            fputcsv($out, [
                $row['user'],
                $row['model'],
                (string)$row['tokens_input'],
                (string)$row['tokens_output'],
                (string)$row['tokens_cache_read'],
                (string)$row['tokens_cache_write'],
                $row['cost_usd']
            ], ',', '"', '\\');
        }

        // TOTAL
        $totalInput = $totalOutput = $totalCacheRead = $totalCacheWrite = 0;
        $totalCost = '0';
        foreach ($rows as $row) {
            $totalInput += $row['tokens_input'];
            $totalOutput += $row['tokens_output'];
            $totalCacheRead += $row['tokens_cache_read'];
            $totalCacheWrite += $row['tokens_cache_write'];
            $totalCost = bcadd($totalCost, $row['cost_usd'], 6);
        }
        fputcsv($out, [
            'TOTAL',
            '',
            (string)$totalInput,
            (string)$totalOutput,
            (string)$totalCacheRead,
            (string)$totalCacheWrite,
            $totalCost
        ], ',', '"', '\\');

        fclose($out);
        return Command::SUCCESS;
    }
}
