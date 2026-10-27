<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\ApplicationServices;
use App\Domain\Money\DecimalMoney;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use DateTimeImmutable;
use DateTimeZone;

#[AsCommand(name: 'app:billing:report', description: 'Print a CSV report of rated usage events.')]
final class BillingReportCommand extends Command
{
    protected static string $defaultName = 'app:billing:report';

    public function __construct(
        private readonly ApplicationServices $app,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Start datetime, ISO-8601 UTC, inclusive')
            ->addOption('to',   null, InputOption::VALUE_REQUIRED, 'End datetime, ISO-8601 UTC, exclusive');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $stderr = $output->getErrorOutput();
        $utc = new DateTimeZone('UTC');

        $from = $this->parseDate($input->getOption('from'), 'from', $stderr);
        if ($from === null) return Command::FAILURE;
        $to = $this->parseDate($input->getOption('to'), 'to', $stderr);
        if ($to === null) return Command::FAILURE;

        $report = $this->app->reportRange($from, $to);

        // Build CSV — write to STDOUT (not getErrorOutput)
        $io = $output; // OutputInterface
        $stream = $io instanceof OutputInterface ? $io : null;

        // Symfony Console: use writeln with StreamOutput or plain echo
        // For strict stdout, we collect lines and print them in one block
        $lines = [];
        $lines[] = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

        foreach ($report['groups'] as $g) {
            $lines[] = implode(',', [
                $g['user'],
                $g['model'],
                (string) $g['input'],
                (string) $g['output'],
                (string) $g['cache_read'],
                (string) $g['cache_write'],
                $g['cost']->format(6),
            ]);
        }

        $t = $report['totals'];
        $lines[] = implode(',', [
            'TOTAL', '',
            (string) $t['input'],
            (string) $t['output'],
            (string) $t['cache_read'],
            (string) $t['cache_write'],
            $t['cost']->format(6),
        ]);

        // Print to stdout directly (avoid stderr)
        fwrite(STDOUT, implode("\n", $lines) . "\n");

        return Command::SUCCESS;
    }

    private function parseDate(?string $raw, string $label, OutputInterface $stderr): ?DateTimeImmutable
    {
        if ($raw === null || $raw === '') return null;
        try {
            $dt = new DateTimeImmutable($raw, new DateTimeZone('UTC'));
            return $dt->setTimezone(new DateTimeZone('UTC'));
        } catch (\Exception) {
            $stderr->writeln("ERROR: --$label: invalid ISO-8601 datetime: $raw");
            return null;
        }
    }
}
