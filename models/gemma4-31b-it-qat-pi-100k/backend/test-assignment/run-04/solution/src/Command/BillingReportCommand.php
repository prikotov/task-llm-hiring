<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\Usage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report')]
class BillingReportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_OPTIONAL, 'Start ISO date');
        $this->addOption('to', null, InputOption::VALUE_OPTIONAL, 'End ISO date');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from') ? new \DateTimeImmutable($input->getOption('from')) : null;
        $to = $input->getOption('to') ? new \DateTimeImmutable($input->getOption('to')) : null;

        $qb = $this->em->createQueryBuilder();
        $qb->select('u')
           ->from(Usage::class, 'u')
           ->where('u.costUsd IS NOT NULL');

        if ($from) {
            $qb->andWhere('u.ts >= :from')->setParameter('from', $from);
        }
        if ($to) {
            $qb->andWhere('u.ts < :to')->setParameter('to', $to);
        }

        $usages = $qb->getQuery()->getResult();

        $groups = [];
        $totals = [
            'tokens_input' => 0,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
            'cost_usd' => '0',
        ];

        foreach ($usages as $usage) {
            $user = $usage->userName;
            $model = $usage->model->provider->code . '/' . $usage->model->code;
            $key = $user . '||' . $model;

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $user,
                    'model' => $model,
                    'tokens_input' => 0,
                    'tokens_output' => 0,
                    'tokens_cache_read' => 0,
                    'tokens_cache_write' => 0,
                    'cost_usd' => '0',
                ];
            }

            $groups[$key]['tokens_input'] += $usage->tokensInput;
            $groups[$key]['tokens_output'] += $usage->tokensOutput;
            $groups[$key]['tokens_cache_read'] += $usage->tokensCacheRead;
            $groups[$key]['tokens_cache_write'] += $usage->tokensCacheWrite;
            $groups[$key]['cost_usd'] = bcadd($groups[$key]['cost_usd'], $usage->costUsd, 6);

            $totals['tokens_input'] += $usage->tokensInput;
            $totals['tokens_output'] += $usage->tokensOutput;
            $totals['tokens_cache_read'] += $usage->tokensCacheRead;
            $totals['tokens_cache_write'] += $usage->tokensCacheWrite;
            $totals['cost_usd'] = bcadd($totals['cost_usd'], $usage->costUsd, 6);
        }

        // Sort by user then model
        uasort($groups, function ($a, $b) {
            if ($a['user'] === $b['user']) {
                return strcmp($a['model'], $b['model']);
            }
            return strcmp($a['user'], $b['user']);
        });

        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');
        foreach ($groups as $group) {
            $output->writeln(sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $group['user'],
                $group['model'],
                $group['tokens_input'],
                $group['tokens_output'],
                $group['tokens_cache_read'],
                $group['tokens_cache_write'],
                $group['cost_usd']
            ));
        }
        
        $output->writeln(sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $totals['tokens_input'],
            $totals['tokens_output'],
            $totals['tokens_cache_read'],
            $totals['tokens_cache_write'],
            $totals['cost_usd']
        ));

        return Command::SUCCESS;
    }
}
