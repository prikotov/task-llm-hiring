<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\ReportService;
use App\Application\Parse\JsonInput;
use App\Exception\BillingException;
use DateTimeImmutable;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'CSV report per (user, model) for a time range; printed to stdout.',
)]
final class ReportCommand extends Command
{
    public function __construct(
        private readonly ReportService $reports,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Range start, inclusive, ISO-8601 UTC (Z), compared with event ts')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Range end, exclusive, ISO-8601 UTC (Z), compared with event ts')
            ->setHelp(
                'Aggregates rated events (unrated are never included) by (user, model) and prints '
                .'the CSV per TASK.md section 7 to stdout: one row per pair '
                .'(totals of token counters and cost_usd, 6 decimal places) plus a TOTAL row. '
                .'`--from` is inclusive, `--to` exclusive; when both are given, from <= to. '
                .'Exit code: 0 on success, 1 on invalid options or errors.'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $errors = $output->getErrorOutput();

        $from = $this->parseRangeOption((string) $input->getOption('from'), '--from', $errors);
        if ($from === false) {
            return Command::FAILURE;
        }
        $to = $this->parseRangeOption((string) $input->getOption('to'), '--to', $errors);
        if ($to === false) {
            return Command::FAILURE;
        }

        if ($from !== null && $to !== null && $from > $to) {
            $errors->writeln('<error>--from must be <= --to</error>');

            return Command::FAILURE;
        }

        try {
            $csv = $this->reports->buildCsv($from, $to);
        } catch (\Throwable $e) {
            $errors->writeln(sprintf('<error>report failed: %s</error>', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->write($csv);

        return Command::SUCCESS;
    }

    private function parseRangeOption(
        string $value,
        string $option,
        OutputInterface $output,
    ): null|DateTimeImmutable|false {
        if ($value === '') {
            return null;
        }

        try {
            return JsonInput::isoUtcDateTime(['value' => $value], 'value', sprintf('%s', $option), BillingException::class);
        } catch (BillingException $e) {
            $output->writeln(sprintf('<error>%s: %s</error>', $option, $e->getMessage()));

            return false;
        }
    }
}
