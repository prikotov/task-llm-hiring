<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Billing\ImportInputException;
use App\Domain\Time\UtcInstant;
use App\Service\Billing\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'CSV-отчёт по рассчитанным событиям (stdout), группировка по (user, model)',
)]
final class BillingReportCommand extends Command
{
    public function __construct(
        private readonly ReportService $report,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'начало периода, ISO-8601 UTC, включительно')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'конец периода, ISO-8601 UTC, исключительно');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = $this->parseInstant($input, 'from');
            $to = $this->parseInstant($input, 'to');
        } catch (ImportInputException $e) {
            $output->writeln('usage error: '.$e->getMessage());

            return Command::FAILURE;
        }

        if ($from === null && $to !== null) {
            $output->writeln('usage error: --from required when --to is given');

            return Command::FAILURE;
        }
        if ($from !== null && $to !== null && $from >= $to) {
            $output->writeln('usage error: --from must be before --to');

            return Command::FAILURE;
        }

        $data = $this->report->build($from, $to);

        // CSV: разделитель «,», перевод строки \n, UTF-8
        $write = static function (string $line) use ($output): void {
            $output->write(str_replace(["\r\n", "\r"], "\n", $line)."\n");
        };

        $write('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');

        foreach ($data['rows'] as $row) {
            $write(implode(',', [
                self::cell($row['user']),
                self::cell($row['model']),
                (string) $row['tokens_input'],
                (string) $row['tokens_output'],
                (string) $row['tokens_cache_read'],
                (string) $row['tokens_cache_write'],
                $row['cost'],
            ]));
        }

        $t = $data['total'];
        $write(implode(',', [
            'TOTAL',
            '',
            (string) $t['tokens_input'],
            (string) $t['tokens_output'],
            (string) $t['tokens_cache_read'],
            (string) $t['tokens_cache_write'],
            $t['cost'],
        ]));

        return Command::SUCCESS;
    }

    private function parseInstant(InputInterface $input, string $name): ?\DateTimeImmutable
    {
        $value = $input->getOption($name);
        if ($value === null || $value === false) {
            return null;
        }

        try {
            return UtcInstant::parse((string) $value);
        } catch (\InvalidArgumentException $e) {
            throw ImportInputException::field(sprintf('--%s', $name), $e->getMessage());
        }
    }

    private static function cell(string $value): string
    {
        if (preg_match('/[",\r\n]/', $value) === 1) {
            return '"'.str_replace('"', '""', $value).'"';
        }

        return $value;
    }
}
