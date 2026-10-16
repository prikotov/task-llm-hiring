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

#[AsCommand(name: 'app:billing:report', description: 'Report billing costs')]
class BillingReportCommand extends Command
{
    public function __construct(private EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_OPTIONAL, 'Start ISO date inclusive');
        $this->addOption('to', null, InputOption::VALUE_OPTIONAL, 'End ISO date exclusive');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $input->getOption('from');
        $to = $input->getOption('to');

        $qb = $this->em->createQueryBuilder();
        $qb->select('e.user', 'e.model', 'SUM(e.tokensInput) as tokensInput', 'SUM(e.tokensOutput) as tokensOutput', 'SUM(e.tokensCacheRead) as tokensCacheRead', 'SUM(e.tokensCacheWrite) as tokensCacheWrite', 'SUM(e.cost) as cost')
            ->from(UsageEvent::class, 'e')
            ->where('e.cost IS NOT NULL');

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

        $qb->groupBy('e.user')
           ->addGroupBy('e.model')
           ->orderBy('e.user', 'ASC')
           ->addOrderBy('e.model', 'ASC');

        $rows = $qb->getQuery()->getResult();

        // output header
        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');

        $totalTokensInput = '0';
        $totalTokensOutput = '0';
        $totalTokensCacheRead = '0';
        $totalTokensCacheWrite = '0';
        $totalCost = '0.000000';

        foreach ($rows as $row) {
            $user = $row['user'];
            $model = $row['model'];
            $tokensInput = (string)$row['tokensInput'];
            $tokensOutput = (string)$row['tokensOutput'];
            $tokensCacheRead = (string)$row['tokensCacheRead'];
            $tokensCacheWrite = (string)$row['tokensCacheWrite'];
            $cost = (string)$row['cost'];
            // ensure 6 decimals
            $costFormatted = number_format((float)$cost, 6, '.', '');
            // But float may lose precision, use bc? For output we can just format with sprintf.
            // We'll format using bc to avoid float.
            $costFormatted = $this->formatDecimal($cost, 6);

            $output->writeln(sprintf('%s,%s,%s,%s,%s,%s,%s', $user, $model, $tokensInput, $tokensOutput, $tokensCacheRead, $tokensCacheWrite, $costFormatted));

            $totalTokensInput = bcadd($totalTokensInput, $tokensInput, 0);
            $totalTokensOutput = bcadd($totalTokensOutput, $tokensOutput, 0);
            $totalTokensCacheRead = bcadd($totalTokensCacheRead, $tokensCacheRead, 0);
            $totalTokensCacheWrite = bcadd($totalTokensCacheWrite, $tokensCacheWrite, 0);
            $totalCost = bcadd($totalCost, $cost, 6);
        }

        $totalCostFormatted = $this->formatDecimal($totalCost, 6);
        $output->writeln(sprintf('TOTAL,,%s,%s,%s,%s,%s', $totalTokensInput, $totalTokensOutput, $totalTokensCacheRead, $totalTokensCacheWrite, $totalCostFormatted));

        return Command::SUCCESS;
    }

    private function formatDecimal(string $value, int $precision): string
    {
        // Normalize
        if ($value === null || $value === '') {
            $value = '0';
        }
        // Ensure decimal separator
        $parts = explode('.', $value);
        $intPart = $parts[0];
        $fracPart = $parts[1] ?? '';
        $fracPart = str_pad($fracPart, $precision, '0', STR_PAD_RIGHT);
        $fracPart = substr($fracPart, 0, $precision);
        return $intPart . '.' . $fracPart;
    }
}
