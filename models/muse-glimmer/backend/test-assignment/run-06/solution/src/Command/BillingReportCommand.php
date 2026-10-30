<?php

namespace App\Command;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report')]
class BillingReportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'ISO start');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'ISO end exclusive');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from') ? new \DateTimeImmutable($input->getOption('from')) : null;
        $to = $input->getOption('to') ? new \DateTimeImmutable($input->getOption('to')) : null;

        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.costUsd IS NOT NULL');
        if ($from) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }
        $events = $qb->getQuery()->getResult();

        $agg = [];
        foreach ($events as $e) {
            $key = $e->getUser() . '|' . $e->getProviderCode() . '/' . $e->getModelCode();
            if (!isset($agg[$key])) {
                $agg[$key] = [
                    'user' => $e->getUser(),
                    'provider' => $e->getProviderCode(),
                    'model' => $e->getModelCode(),
                    'tokens_input' => 0,
                    'tokens_output' => 0,
                    'tokens_cache_read' => 0,
                    'tokens_cache_write' => 0,
                    'cost' => '0',
                ];
            }
            $agg[$key]['tokens_input'] += $e->getTokensInput();
            $agg[$key]['tokens_output'] += $e->getTokensOutput();
            $agg[$key]['tokens_cache_read'] += $e->getTokensCacheRead();
            $agg[$key]['tokens_cache_write'] += $e->getTokensCacheWrite();
            $agg[$key]['cost'] = bcadd($agg[$key]['cost'], $e->getCostUsd(), 6);
        }

        $rows = array_values($agg);
        usort($rows, fn($a,$b)=> $a['user'] <=> $b['user'] ?: ($a['provider'].'/'.$a['model'] <=> $b['provider'].'/'.$b['model']));

        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');
        $totalTokensInput = 0;
        $totalTokensOutput = 0;
        $totalTokensCacheRead = 0;
        $totalTokensCacheWrite = 0;
        $totalCost = '0';
        foreach ($rows as $r) {
            $modelStr = $r['provider'].'/'.$r['model'];
            $cost = number_format((float)$r['cost'],6,'.','');
            $output->writeln(sprintf('%s,%s,%d,%d,%d,%d,%s',
                $r['user'], $modelStr, $r['tokens_input'], $r['tokens_output'], $r['tokens_cache_read'], $r['tokens_cache_write'], $cost));
            $totalTokensInput += $r['tokens_input'];
            $totalTokensOutput += $r['tokens_output'];
            $totalTokensCacheRead += $r['tokens_cache_read'];
            $totalTokensCacheWrite += $r['tokens_cache_write'];
            $totalCost = bcadd($totalCost, $r['cost'], 6);
        }
        $totalCostFmt = number_format((float)$totalCost,6,'.','');
        $output->writeln(sprintf('TOTAL,,%d,%d,%d,%d,%s', $totalTokensInput, $totalTokensOutput, $totalTokensCacheRead, $totalTokensCacheWrite, $totalCostFmt));
        return Command::SUCCESS;
    }
}
