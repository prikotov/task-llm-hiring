<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\RateService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'Собрать отчёт по user/model за период [from, to).')]
final class ReportCommand extends Command
{
    public function __construct(private readonly RateService $rater)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption(
                'from',
                null,
                InputOption::VALUE_REQUIRED,
                'Начало периода (ISO-8601 UTC), включительно. По умолчанию — не ограничено.'
            )
            ->addOption(
                'to',
                null,
                InputOption::VALUE_REQUIRED,
                'Конец периода (ISO-8601 UTC), не включает. По умолчанию — не ограничено.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $fromOpt = $input->getOption('from');
        $toOpt = $input->getOption('to');
        $from = $fromOpt !== null
            ? new \DateTimeImmutable($fromOpt)
            : new \DateTimeImmutable('1970-01-01T00:00:00Z');
        $to = $toOpt !== null
            ? new \DateTimeImmutable($toOpt)
            : new \DateTimeImmutable('2100-01-01T00:00:00Z');

        $rows = $this->rater->report($from, $to);

        // CSV — строго stdout (свой stdout, без служебных заголовков).
        $out = $output->getStream();

        \fwrite($out, "user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd\n");

        $total = end($rows);
        foreach ($rows as $r) {
            if ($r['user'] === 'TOTAL') {
                continue;
            }
            \fwrite($out, sprintf(
                "%s,%s,%d,%d,%d,%d,%s\n",
                $r['user'],
                $r['model'],
                $r['tokens_input'],
                $r['tokens_output'],
                $r['tokens_cache_read'],
                $r['tokens_cache_write'],
                $r['cost_usd'],
            ));
        }

        \fwrite($out, sprintf(
            "TOTAL,,%d,%d,%d,%d,%s\n",
            $total['tokens_input'],
            $total['tokens_output'],
            $total['tokens_cache_read'],
            $total['tokens_cache_write'],
            $total['cost_usd'],
        ));

        return 0;
    }
}
