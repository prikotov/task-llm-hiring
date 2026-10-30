<?php

declare(strict_types=1);

namespace App\Command;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Generate billing report CSV')]
class BillingReportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'From ISO8601 inclusive')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'To ISO8601 exclusive');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fromOpt = $input->getOption('from');
        $toOpt = $input->getOption('to');

        $qb = $this->em->createQueryBuilder();
        $qb->select('e.user, e.providerCode, e.modelCode')
            ->addSelect('SUM(e.tokensInput) as tokensInput')
            ->addSelect('SUM(e.tokensOutput) as tokensOutput')
            ->addSelect('SUM(e.tokensCacheRead) as tokensCacheRead')
            ->addSelect('SUM(e.tokensCacheWrite) as tokensCacheWrite')
            ->addSelect('SUM(e.costUsd) as costUsd')
            ->from(UsageEvent::class, 'e')
            ->where('e.costUsd IS NOT NULL');

        if ($fromOpt) {
            $from = new \DateTimeImmutable($fromOpt);
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($toOpt) {
            $to = new \DateTimeImmutable($toOpt);
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        $qb->groupBy('e.user, e.providerCode, e.modelCode')
            ->orderBy('e.user', 'ASC')
            ->addOrderBy('e.providerCode', 'ASC')
            ->addOrderBy('e.modelCode', 'ASC');

        $rows = $qb->getQuery()->getResult();

        // Prepare output
        $output->write("user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n");

        $totalTokensInput = '0';
        $totalTokensOutput = '0';
        $totalTokensCacheRead = '0';
        $totalTokensCacheWrite = '0';
        $totalCost = '0.0';

        bcscale(6);
        foreach ($rows as $row) {
            $user = $row['user'];
            $provider = $row['providerCode'];
            $modelCode = $row['modelCode'];
            $model = $provider . '/' . $modelCode;
            $tokensInput = (string)$row['tokensInput'];
            $tokensOutput = (string)$row['tokensOutput'];
            $tokensCacheRead = (string)$row['tokensCacheRead'];
            $tokensCacheWrite = (string)$row['tokensCacheWrite'];
            $cost = $row['costUsd'] ?? '0';
            // ensure cost format 6 decimals
            if (strpos($cost, '.') === false) {
                $cost = $cost . '.000000';
            } else {
                $parts = explode('.', $cost);
                $frac = str_pad(substr($parts[1],0,6),6,'0',STR_PAD_RIGHT);
                $cost = $parts[0] . '.' . substr($frac,0,6);
            }

            $output->write(sprintf("%s,%s,%s,%s,%s,%s,%s\n",
                $user,
                $model,
                $tokensInput,
                $tokensOutput,
                $tokensCacheRead,
                $tokensCacheWrite,
                $cost
            ));

            $totalTokensInput = bcadd($totalTokensInput, $tokensInput, 0);
            $totalTokensOutput = bcadd($totalTokensOutput, $tokensOutput, 0);
            $totalTokensCacheRead = bcadd($totalTokensCacheRead, $tokensCacheRead, 0);
            $totalTokensCacheWrite = bcadd($totalTokensCacheWrite, $tokensCacheWrite, 0);
            $totalCost = bcadd($totalCost, $cost, 6);
        }

        // total row
        $totalCostFormatted = $this->formatDecimal($totalCost, 6);
        $output->write(sprintf("TOTAL,,%s,%s,%s,%s,%s\n",
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
        if (strpos($value, '.') === false) {
            return $value . '.' . str_repeat('0', $precision);
        }
        [$int, $frac] = explode('.', $value, 2);
        $frac = str_pad($frac, $precision, '0', STR_PAD_RIGHT);
        $frac = substr($frac, 0, $precision);
        return $int . '.' . $frac;
    }
}
