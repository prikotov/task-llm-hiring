<?php

namespace App\Command;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class BillingReportCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('app:billing:report')
            ->setDescription('Generate billing report CSV')
            ->addOption('from', null, InputOption::VALUE_OPTIONAL, 'Start date (ISO-8601)')
            ->addOption('to', null, InputOption::VALUE_OPTIONAL, 'End date (ISO-8601)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from') ? new \DateTimeImmutable($input->getOption('from')) : null;
        $to = $input->getOption('to') ? new \DateTimeImmutable($input->getOption('to')) : null;

        $qb = $this->entityManager->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.isRated = true');

        if ($from) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        $events = $qb->getQuery()->getResult();

        $aggregates = [];
        $totalTokensInput = 0;
        $totalTokensOutput = 0;
        $totalTokensCacheRead = 0;
        $totalTokensCacheWrite = 0;
        $totalCost = '0.000000';

        foreach ($events as $event) {
            $user = $event->username;
            $modelStr = (string)$event->model;
            $key = $user . '||' . $modelStr;

            if (!isset($aggregates[$key])) {
                $aggregates[$key] = [
                    'user' => $user,
                    'model' => $modelStr,
                    'tokens_input' => 0,
                    'tokens_output' => 0,
                    'tokens_cache_read' => 0,
                    'tokens_cache_write' => 0,
                    'cost_usd' => '0.000000',
                ];
            }

            $aggregates[$key]['tokens_input'] += $event->tokensInput;
            $aggregates[$key]['tokens_output'] += $event->tokensOutput;
            $aggregates[$key]['tokens_cache_read'] += $event->tokensCacheRead;
            $aggregates[$key]['tokens_cache_write'] += $event->tokensCacheWrite;
            $aggregates[$key]['cost_usd'] = bcadd($aggregates[$key]['cost_usd'], $event->calculatedCost, 6);


            $totalTokensInput += $event->tokensInput;
            $totalTokensOutput += $event->tokensOutput;
            $totalTokensCacheRead += $event->tokensCacheRead;
            $totalTokensCacheWrite += $event->tokensCacheWrite;
            $totalCost = bcadd($totalCost, $event->calculatedCost, 6);
        }

        // Sort by user then model
        usort($aggregates, function ($a, $b) {
            return $a['user'] === $b['user'] 
                ? strcmp($a['model'], $b['model']) 
                : strcmp($a['user'], $b['user']);
        });
        
        // Correction: the above usort needs an array of values, not an associative array.
        // Let's re-do this.
        
        $reportRows = array_values($aggregates);
        usort($reportRows, function ($a, $b) {
            if ($a['user'] !== $b['user']) {
                return strcmp($a['user'], $b['user']);
            }
            return strcmp($a['model'], $b['model']);
        });

        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');
        foreach ($reportRows as $row) {
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
        }

        $output->writeln(sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $totalTokensInput,
            $totalTokensOutput,
            $totalTokensCacheRead,
            $totalTokensCacheWrite,
            $totalCost
        ));

        return 0;
    }
}
