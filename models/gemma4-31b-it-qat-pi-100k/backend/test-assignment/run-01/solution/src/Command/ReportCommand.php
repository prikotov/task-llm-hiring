<?php

namespace App\Command;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ReportCommand extends Command
{
    public function __construct(
        private EntityManagerInterface $entityManager
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('app:billing:report')
            ->addOption('from', null, InputOption::VALUE_OPTIONAL, 'Start date ISO-8601')
            ->addOption('to', null, InputOption::VALUE_OPTIONAL, 'End date ISO-8601');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from');
        $to = $input->getOption('to');

        $qb = $this->entityManager->createQueryBuilder();
        $qb->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.costUsd IS NOT NULL');

        if ($from) {
            $qb->andWhere('e.ts >= :from')
                ->setParameter('from', new \DateTimeImmutable($from));
        }

        if ($to) {
            $qb->andWhere('e.ts < :to')
                ->setParameter('to', new \DateTimeImmutable($to));
        }

        $events = $qb->getQuery()->getResult();

        $data = [];
        $total = [
            'tokens_input' => 0,
            'tokens_output' => 0,
            'tokens_cache_read' => 0,
            'tokens_cache_write' => 0,
            'cost_usd' => '0',
        ];

        foreach ($events as $event) {
            $key = $event->getUser() . '|' . $event->getProvider() . '/' . $event->getModel();
            if (!isset($data[$key])) {
                $data[$key] = [
                    'user' => $event->getUser(),
                    'model' => $event->getProvider() . '/' . $event->getModel(),
                    'tokens_input' => 0,
                    'tokens_output' => 0,
                    'tokens_cache_read' => 0,
                    'tokens_cache_write' => 0,
                    'cost_usd' => '0',
                ];
            }

            $data[$key]['tokens_input'] += $event->getTokensInput();
            $data[$key]['tokens_output'] += $event->getTokensOutput();
            $data[$key]['tokens_cache_read'] += $event->getTokensCacheRead();
            $data[$key]['tokens_cache_write'] += $event->getTokensCacheWrite();
            $data[$key]['cost_usd'] = bcadd($data[$key]['cost_usd'], $event->getCostUsd(), 6);

            $total['tokens_input'] += $event->getTokensInput();
            $total['tokens_output'] += $event->getTokensOutput();
            $total['tokens_cache_read'] += $event->getTokensCacheRead();
            $total['tokens_cache_write'] += $event->getTokensCacheWrite();
            $total['cost_usd'] = bcadd($total['cost_usd'], $event->getCostUsd(), 6);
        }

        ksort($data); // This is not quite right, we need to sort by user then model.
        
        // Proper sort
        usort($data, function($a, $b) {
            if ($a['user'] !== $b['user']) {
                return strcmp($a['user'], $b['user']);
            }
            return strcmp($a['model'], $b['model']);
        });

        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');
        foreach ($data as $row) {
            $output->writeln(sprintf('%s,%s,%d,%d,%d,%d,%s',
                $row['user'],
                $row['model'],
                $row['tokens_input'],
                $row['tokens_output'],
                $row['tokens_cache_read'],
                $row['tokens_cache_write'],
                $row['cost_usd']
            ));
        }

        $output->writeln(sprintf('TOTAL,,%d,%d,%d,%d,%s',
            $total['tokens_input'],
            $total['tokens_output'],
            $total['tokens_cache_read'],
            $total['tokens_cache_write'],
            $total['cost_usd']
        ));

        return Command::SUCCESS;
    }
}
