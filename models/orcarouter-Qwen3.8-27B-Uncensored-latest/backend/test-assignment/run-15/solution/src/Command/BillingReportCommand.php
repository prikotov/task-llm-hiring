<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\ValueObject\Money;
use App\Repository\UsageEventRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'Print CSV report of rated usage grouped by (user, provider/model).',
)]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly UsageEventRepository $events)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'ISO-8601 UTC lower bound (inclusive) for event ts')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'ISO-8601 UTC upper bound (exclusive) for event ts');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $this->parseIso($input->getOption('from'));
        $to   = $this->parseIso($input->getOption('to'));

        $rows = $this->events->aggregateReport($from, $to);

        $totalIn  = 0;
        $totalOut = 0;
        $totalCr  = 0;
        $totalCw  = 0;
        $sumCost  = '0';

        $body = ['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd'];

        foreach ($rows as $r) {
            $tin  = (int) ($r['tin']  ?? 0);
            $tout = (int) ($r['tout'] ?? 0);
            $tcr  = (int) ($r['tcr']  ?? 0);
            $tcw  = (int) ($r['tcw']  ?? 0);
            $usd  = Money::from((string)($r['total'] ?? '0'))->roundTo(6)->format(6);

            $totalIn  += $tin;
            $totalOut += $tout;
            $totalCr  += $tcr;
            $totalCw  += $tcw;
            $sumCost  = bcadd($sumCost, $usd, 20);

            $body[] = implode(',', [
                (string) ($r['user'] ?? ''),
                (string) ($r['model'] ?? ''),
                (string) $tin,
                (string) $tout,
                (string) $tcr,
                (string) $tcw,
                $usd,
            ]);
        }

        $body[] = implode(',', [
            'TOTAL',
            '',
            (string) $totalIn,
            (string) $totalOut,
            (string) $totalCr,
            (string) $totalCw,
            Money::from($sumCost)->roundTo(6)->format(6),
        ]);

        \fwrite($output->getStream(), implode("\n", $body) . "\n");
        return self::SUCCESS;
    }

    private function parseIso(?string $v): ?string
    {
        if ($v === null || $v === '') {
            return null;
        }
        try {
            $dt = new \DateTimeImmutable($v, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new \InvalidArgumentException("invalid ISO-8601 datetime: {$v}");
        }
        return $dt->format('c');
    }
}
