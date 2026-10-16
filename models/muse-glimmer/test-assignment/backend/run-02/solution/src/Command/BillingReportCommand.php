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
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_OPTIONAL, 'ISO-8601 start inclusive');
        $this->addOption('to', null, InputOption::VALUE_OPTIONAL, 'ISO-8601 end exclusive');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        bcscale(16);
        $qb = $this->em->getRepository(UsageEvent::class)->createQueryBuilder('e')
            ->where('e.costUsd IS NOT NULL');

        $from = $input->getOption('from');
        $to = $input->getOption('to');

        if ($from) {
            $fromDt = new \DateTimeImmutable($from);
            $qb->andWhere('e.ts >= :from')->setParameter('from', $fromDt);
        }
        if ($to) {
            $toDt = new \DateTimeImmutable($to);
            $qb->andWhere('e.ts < :to')->setParameter('to', $toDt);
        }

        $events = $qb->getQuery()->getResult();

        // Group
        $groups = [];
        foreach ($events as $event) {
            /** @var UsageEvent $event */
            $user = $event->getUser();
            $model = $event->getProviderCode() . '/' . $event->getModelCode();
            $key = $user . '|' . $model;
            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $user,
                    'model' => $model,
                    'tokens_input' => '0',
                    'tokens_output' => '0',
                    'tokens_cache_read' => '0',
                    'tokens_cache_write' => '0',
                    'cost_usd' => '0',
                ];
            }
            $groups[$key]['tokens_input'] = bcadd($groups[$key]['tokens_input'], $event->getTokensInput(), 0);
            $groups[$key]['tokens_output'] = bcadd($groups[$key]['tokens_output'], $event->getTokensOutput(), 0);
            $groups[$key]['tokens_cache_read'] = bcadd($groups[$key]['tokens_cache_read'], $event->getTokensCacheRead(), 0);
            $groups[$key]['tokens_cache_write'] = bcadd($groups[$key]['tokens_cache_write'], $event->getTokensCacheWrite(), 0);
            $groups[$key]['cost_usd'] = bcadd($groups[$key]['cost_usd'], $event->getCostUsd(), 6);
        }

        // Sort
        usort($groups, function ($a, $b) {
            $c = strcmp($a['user'], $b['user']);
            if ($c !== 0) return $c;
            return strcmp($a['model'], $b['model']);
        });

        // Output CSV
        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');
        $totalTokensInput = '0';
        $totalTokensOutput = '0';
        $totalTokensCacheRead = '0';
        $totalTokensCacheWrite = '0';
        $totalCost = '0';

        foreach ($groups as $g) {
            $costFormatted = number_format((float)$g['cost_usd'], 6, '.', '');
            // Use bc to format with trailing zeros
            // Since bcadd maintains, we need format.
            $costFormatted = $this->formatDecimal($g['cost_usd'], 6);
            $output->writeln(sprintf(
                '%s,%s,%s,%s,%s,%s,%s',
                $g['user'],
                $g['model'],
                $g['tokens_input'],
                $g['tokens_output'],
                $g['tokens_cache_read'],
                $g['tokens_cache_write'],
                $costFormatted
            ));
            $totalTokensInput = bcadd($totalTokensInput, $g['tokens_input'], 0);
            $totalTokensOutput = bcadd($totalTokensOutput, $g['tokens_output'], 0);
            $totalTokensCacheRead = bcadd($totalTokensCacheRead, $g['tokens_cache_read'], 0);
            $totalTokensCacheWrite = bcadd($totalTokensCacheWrite, $g['tokens_cache_write'], 0);
            $totalCost = bcadd($totalCost, $g['cost_usd'], 6);
        }

        $totalCostFmt = $this->formatDecimal($totalCost, 6);
        $output->writeln(sprintf(
            'TOTAL,,%s,%s,%s,%s,%s',
            $totalTokensInput,
            $totalTokensOutput,
            $totalTokensCacheRead,
            $totalTokensCacheWrite,
            $totalCostFmt
        ));

        return Command::SUCCESS;
    }

    private function formatDecimal(string $value, int $precision): string
    {
        // Ensure exactly precision decimals, using bc math
        // Normalize
        $value = bcadd($value, '0', $precision);
        // bcadd with scale will produce with required decimals?
        // Might need to pad.
        // Quick approach: use sprintf with bc? Might just use number_format with bc to string.
        // We'll rely on bcadd to produce decimal with scale.
        // To guarantee trailing zeros, we can split.
        [$int, $frac] = explode('.', $value . '.');
        $frac = substr(str_pad($frac, $precision, '0'), 0, $precision);
        return $int . '.' . $frac;
    }
}
