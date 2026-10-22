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
final class BillingReportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Start timestamp ISO8601 inclusive')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'End timestamp ISO8601 exclusive');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from');
        $to = $input->getOption('to');

        $qb = $this->em->createQueryBuilder();
        $qb->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.costUsd IS NOT NULL');

        if ($from) {
            $fromDt = new \DateTimeImmutable($from);
            $qb->andWhere('e.ts >= :from')
               ->setParameter('from', $fromDt);
        }
        if ($to) {
            $toDt = new \DateTimeImmutable($to);
            $qb->andWhere('e.ts < :to')
               ->setParameter('to', $toDt);
        }

        $events = $qb->getQuery()->getResult();

        $groups = [];
        $totalTokensInput = 0;
        $totalTokensOutput = 0;
        $totalTokensCacheRead = 0;
        $totalTokensCacheWrite = 0;
        $totalCost = '0';

        foreach ($events as $event) {
            /** @var UsageEvent $event */
            $user = $event->getUser();
            $model = $event->getProviderCode() . '/' . $event->getModelCode();
            $key = $user . "\0" . $model;

            if (!isset($groups[$key])) {
                $groups[$key] = [
                    'user' => $user,
                    'model' => $model,
                    'tokens_input' => 0,
                    'tokens_output' => 0,
                    'tokens_cache_read' => 0,
                    'tokens_cache_write' => 0,
                    'cost' => '0',
                ];
            }

            $groups[$key]['tokens_input'] += $event->getTokensInput();
            $groups[$key]['tokens_output'] += $event->getTokensOutput();
            $groups[$key]['tokens_cache_read'] += $event->getTokensCacheRead();
            $groups[$key]['tokens_cache_write'] += $event->getTokensCacheWrite();

            $cost = $event->getCostUsd() ?? '0';
            $groups[$key]['cost'] = bcadd($groups[$key]['cost'], $cost, 6);

            $totalTokensInput += $event->getTokensInput();
            $totalTokensOutput += $event->getTokensOutput();
            $totalTokensCacheRead += $event->getTokensCacheRead();
            $totalTokensCacheWrite += $event->getTokensCacheWrite();
            $totalCost = bcadd($totalCost, $cost, 6);
        }

        // sort
        usort($groups, function ($a, $b) {
            $c = strcmp($a['user'], $b['user']);
            if ($c !== 0) {
                return $c;
            }
            return strcmp($a['model'], $b['model']);
        });

        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');
        foreach ($groups as $g) {
            $costFormatted = number_format((float)$g['cost'], 6, '.', '');
            // ensure 6 decimals via bc
            // Use bc to format
            $costFormatted = $this->formatDecimal($g['cost'], 6);
            $output->writeln(sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $g['user'],
                $g['model'],
                $g['tokens_input'],
                $g['tokens_output'],
                $g['tokens_cache_read'],
                $g['tokens_cache_write'],
                $costFormatted
            ));
        }

        // total row
        $totalCostFormatted = $this->formatDecimal($totalCost, 6);
        $output->writeln(sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $totalTokensInput,
            $totalTokensOutput,
            $totalTokensCacheRead,
            $totalTokensCacheWrite,
            $totalCostFormatted
        ));

        return Command::SUCCESS;
    }

    private function formatDecimal(string $value, int $precision): string
    {
        // ensure exactly precision decimals
        $value = $value ?: '0';
        if (strpos($value, '.') === false) {
            return $value . '.' . str_repeat('0', $precision);
        }
        [$int, $dec] = explode('.', $value, 2);
        $dec = str_pad($dec, $precision, '0', STR_PAD_RIGHT);
        $dec = substr($dec, 0, $precision);
        return $int . '.' . $dec;
    }
}
