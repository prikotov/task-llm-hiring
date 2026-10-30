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
        $this->addOption('from', null, InputOption::VALUE_OPTIONAL, 'ISO start inclusive');
        $this->addOption('to', null, InputOption::VALUE_OPTIONAL, 'ISO end exclusive');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from');
        $to = $input->getOption('to');

        $qb = $this->em->getRepository(UsageEvent::class)->createQueryBuilder('e')
            ->where('e.isRated = true')
            ->andWhere('e.costUsd IS NOT NULL');

        if ($from) {
            $fromDt = new \DateTimeImmutable($from);
            $qb->andWhere('e.ts >= :from')->setParameter('from', $fromDt);
        }
        if ($to) {
            $toDt = new \DateTimeImmutable($to);
            $qb->andWhere('e.ts < :to')->setParameter('to', $toDt);
        }

        $events = $qb->getQuery()->getResult();

        $groups = [];
        foreach ($events as $e) {
            $user = $e->getUser();
            $model = $e->getProviderCode() . '/' . $e->getModelCode();
            $key = $user . '|' . $model;
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $user,
                    'model' => $model,
                    'tokens_input' => 0,
                    'tokens_output' => 0,
                    'tokens_cache_read' => 0,
                    'tokens_cache_write' => 0,
                    'cost_usd' => '0.000000',
                ];
            }
            $groups[$key]['tokens_input'] += $e->getTokensInput();
            $groups[$key]['tokens_output'] += $e->getTokensOutput();
            $groups[$key]['tokens_cache_read'] += $e->getTokensCacheRead();
            $groups[$key]['tokens_cache_write'] += $e->getTokensCacheWrite();
            // sum costs as decimal strings
            $groups[$key]['cost_usd'] = bcadd($groups[$key]['cost_usd'], $e->getCostUsd() ?? '0', 6);
        }

        // sort
        $list = array_values($groups);
        usort($list, function($a,$b){
            $c = strcmp($a['user'], $b['user']);
            if ($c !== 0) return $c;
            return strcmp($a['model'], $b['model']);
        });

        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');
        $totalTokensInput = 0;
        $totalTokensOutput = 0;
        $totalTokensCacheRead = 0;
        $totalTokensCacheWrite = 0;
        $totalCost = '0.000000';
        foreach ($list as $row) {
            $output->writeln(sprintf('%s,%s,%d,%d,%d,%d,%s',
                $row['user'],
                $row['model'],
                $row['tokens_input'],
                $row['tokens_output'],
                $row['tokens_cache_read'],
                $row['tokens_cache_write'],
                $row['cost_usd']
            ));
            $totalTokensInput += $row['tokens_input'];
            $totalTokensOutput += $row['tokens_output'];
            $totalTokensCacheRead += $row['tokens_cache_read'];
            $totalTokensCacheWrite += $row['tokens_cache_write'];
            $totalCost = bcadd($totalCost, $row['cost_usd'], 6);
        }
        $output->writeln(sprintf('TOTAL,,%d,%d,%d,%d,%s',
            $totalTokensInput,
            $totalTokensOutput,
            $totalTokensCacheRead,
            $totalTokensCacheWrite,
            $totalCost
        ));
        return Command::SUCCESS;
    }
}
