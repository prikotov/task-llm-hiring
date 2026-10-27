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

#[AsCommand(name: 'app:billing:report', description: 'Report billing')]
class BillingReportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em) { parent::__construct(); }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_OPTIONAL, 'From ISO8601 inclusive');
        $this->addOption('to', null, InputOption::VALUE_OPTIONAL, 'To ISO8601 exclusive');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from');
        $to = $input->getOption('to');

        $qb = $this->em->createQueryBuilder();
        $qb->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.cost IS NOT NULL');

        if ($from) {
            $fromDt = new \DateTimeImmutable($from);
            $qb->andWhere('e.ts >= :from')->setParameter('from', $fromDt);
        }
        if ($to) {
            $toDt = new \DateTimeImmutable($to);
            $qb->andWhere('e.ts < :to')->setParameter('to', $toDt);
        }

        /** @var UsageEvent[] $events */
        $events = $qb->getQuery()->getResult();

        $agg = [];
        foreach ($events as $e) {
            $user = $e->getUser();
            $model = $e->getProvider() . '/' . $e->getModel();
            $key = $user . '|' . $model;
            if (!isset($agg[$key])) {
                $agg[$key] = [
                    'user' => $user,
                    'model' => $model,
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
            // sum costs as strings with bcadd
            $agg[$key]['cost'] = bcadd($agg[$key]['cost'], $e->getCost(), 6);
        }

        // sort
        $rows = array_values($agg);
        usort($rows, function($a,$b){
            $c = strcmp($a['user'], $b['user']);
            if ($c !== 0) return $c;
            return strcmp($a['model'], $b['model']);
        });

        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');
        $totalTokensIn = 0;
        $totalTokensOut = 0;
        $totalTokensCr = 0;
        $totalTokensCw = 0;
        $totalCost = '0';
        foreach ($rows as $r) {
            $output->writeln(sprintf(
                '%s,%s,%d,%d,%d,%d,%.6f',
                $r['user'],
                $r['model'],
                $r['tokens_input'],
                $r['tokens_output'],
                $r['tokens_cache_read'],
                $r['tokens_cache_write'],
                (float)$r['cost'] // ensure 6 decimals formatted
            ));
            $totalTokensIn += $r['tokens_input'];
            $totalTokensOut += $r['tokens_output'];
            $totalTokensCr += $r['tokens_cache_read'];
            $totalTokensCw += $r['tokens_cache_write'];
            $totalCost = bcadd($totalCost, $r['cost'], 6);
        }
        // Format total cost with exactly 6 decimals
        $totalCostStr = number_format((float)$totalCost, 6, '.', '');
        $output->writeln(sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $totalTokensIn,
            $totalTokensOut,
            $totalTokensCr,
            $totalTokensCw,
            $totalCostStr
        ));

        return Command::SUCCESS;
    }
}
