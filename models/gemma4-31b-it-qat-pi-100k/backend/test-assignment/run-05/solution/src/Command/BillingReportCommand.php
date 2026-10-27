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

#[AsCommand(name: 'app:billing:report')]
class BillingReportCommand extends Command
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Generates billing report')
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Start date (ISO-8601)')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'End date (ISO-8601)');
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

        $groups = [];
        $totals = [
            'tokens_input' => 0,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
            'cost_usd' => '0',
        ];

        foreach ($events as $event) {
            $groupKey = $event->getUser() . '||' . $event->getProvider() . '/' . $event->getModel();
            if (!isset($groups[$groupKey])) {
                $groups[$groupKey] = [
                    'user' => $event->getUser(),
                    'model' => $event->getProvider() . '/' . $event->getModel(),
                    'tokens_input' => 0,
                    'tokens_output' => 0,
                    'tokens_cache_read' => 0,
                    'tokens_cache_write' => 0,
                    'cost_usd' => '0',
                ];
            }

            $groups[$groupKey]['tokens_input'] += $event->getTokensInput();
            $groups[$groupKey]['tokens_output'] += $event->getTokensOutput();
            $groups[$groupKey]['tokens_cache_read'] += $event->getTokensCacheRead();
            $groups[$groupKey]['tokens_cache_write'] += $event->getTokensCacheWrite();
            $groups[$groupKey]['cost_usd'] = bcadd($groups[$groupKey]['cost_usd'], $event->getCost() ?? '0', 6);

            $totals['tokens_input'] += $event->getTokensInput();
            $totals['tokens_output'] += $event->getTokensOutput();
            $totals['tokens_cache_read'] += $event->getTokensCacheRead();
            $totals['tokens_cache_write'] += $event->getTokensCacheWrite();
            $totals['cost_usd'] = bcadd($totals['cost_usd'], $event->getCost() ?? '0', 6);
        }

        usort($groups, function ($a, $b) {
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
                number_format((float)$group['cost_usd'], 6, '.', '')
            ));
        }
        
        $output->writeln(sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $totals['tokens_input'],
            $totals['tokens_output'],
            $totals['tokens_cache_read'],
            $totals['tokens_cache_write'],
            number_format((float)$totals['cost_usd'], 6, '.', '')
        ));

        return Command::SUCCESS;
    }
}
