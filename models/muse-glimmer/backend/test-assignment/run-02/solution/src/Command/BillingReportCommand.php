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

#[AsCommand(name: 'app:billing:report', description: 'Report billing CSV')]
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

        $qb = $this->em->getRepository(UsageEvent::class)->createQueryBuilder('e')
            ->where('e.cost IS NOT NULL');

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
        $totalTokensInput = 0;
        $totalTokensOutput = 0;
        $totalTokensCacheRead = 0;
        $totalTokensCacheWrite = 0;
        $totalCost = '0.000000';

        foreach ($events as $e) {
            $user = $e->getUser();
            $modelKey = $e->getProviderCode() . '/' . $e->getModelCode();
            $key = $user . "\0" . $modelKey;
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $user,
                    'model' => $modelKey,
                    'tokens_input' => 0,
                    'tokens_output' => 0,
                    'tokens_cache_read' => 0,
                    'tokens_cache_write' => 0,
                    'cost' => '0',
                ];
            }
            $groups[$key]['tokens_input'] += $e->getTokensInput();
            $groups[$key]['tokens_output'] += $e->getTokensOutput();
            $groups[$key]['tokens_cache_read'] += $e->getTokensCacheRead();
            $groups[$key]['tokens_cache_write'] += $e->getTokensCacheWrite();
            $groups[$key]['cost'] = bcadd($groups[$key]['cost'], $e->getCost(), 6);

            $totalTokensInput += $e->getTokensInput();
            $totalTokensOutput += $e->getTokensOutput();
            $totalTokensCacheRead += $e->getTokensCacheRead();
            $totalTokensCacheWrite += $e->getTokensCacheWrite();
            $totalCost = bcadd($totalCost, $e->getCost(), 6);
        }

        $rows = [];
        foreach ($groups as $g) {
            $rows[] = $g;
        }
        usort($rows, function ($a, $b) {
            $c = strcmp($a['user'], $b['user']);
            if ($c !== 0) return $c;
            return strcmp($a['model'], $b['model']);
        });

        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');
        foreach ($rows as $r) {
            $costFmt = number_format((float)$r['cost'], 6, '.', '');
            // Use bc formatted to 6 decimals
            $costFmt = $this->formatDecimal($r['cost'], 6);
            $output->writeln(sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $r['user'],
                $r['model'],
                $r['tokens_input'],
                $r['tokens_output'],
                $r['tokens_cache_read'],
                $r['tokens_cache_write'],
                $costFmt
            ));
        }
        $totalCostFmt = $this->formatDecimal($totalCost, 6);
        $output->writeln(sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $totalTokensInput,
            $totalTokensOutput,
            $totalTokensCacheRead,
            $totalTokensCacheWrite,
            $totalCostFmt
        ));

        return Command::SUCCESS;
    }

    private function formatDecimal(string $value, int $decimals): string
    {
        if (strpos($value, '.') === false) {
            return $value . '.' . str_repeat('0', $decimals);
        }
        [$int, $frac] = explode('.', $value);
        $frac = str_pad($frac, $decimals, '0', STR_PAD_RIGHT);
        $frac = substr($frac, 0, $decimals);
        return $int . '.' . $frac;
    }
}
