<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Parsers\Scalar;
use App\Application\ReportService;
use App\Domain\Exception\ImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'app:billing:report', description: 'CSV-отчёт расходов по (user, model) за период в stdout')]
final class AppBillingReportCommand extends Command
{
    private const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';
    private const FORMAT = 'Y-m-d\TH:i:s\Z';

    public function __construct(private readonly ReportService $service)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (включительно), ISO-8601 UTC')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (исключительно), ISO-8601 UTC');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $this->parseInstant($input->getOption('from'), '$from', $output);
        if ($from === false) {
            return Command::FAILURE;
        }
        $to = $this->parseInstant($input->getOption('to'), '$to', $output);
        if ($to === false) {
            return Command::FAILURE;
        }

        if ($from !== null && $to !== null && $from > $to) {
            $output->getErrorOutput()->writeln('Некорректный период: from позже to.');
            return Command::FAILURE;
        }

        try {
            $report = $this->service->report($from, $to);
        } catch (\Throwable $e) {
            $output->getErrorOutput()->writeln('Не удалось построить отчёт: '.$e->getMessage());
            return Command::FAILURE;
        }

        // CSV: UTF-8, разделитель ",", перевод строки "\n".
        $out = self::HEADER.PHP_EOL;
        foreach ($report['rows'] as $row) {
            $out .= implode(',', [
                (string) $row['user'],
                (string) $row['model'],
                (string) $row['ti'],
                (string) $row['to'],
                (string) $row['cr'],
                (string) $row['cw'],
                (string) $row['cost'],
            ]).PHP_EOL;
        }
        $out .= implode(',', [
            'TOTAL',
            '',
            (string) $report['total']['ti'],
            (string) $report['total']['to'],
            (string) $report['total']['cr'],
            (string) $report['total']['cw'],
            (string) $report['total']['cost'],
        ]).PHP_EOL;

        $output->write($out);

        return Command::SUCCESS;
    }

    /**
     * Валидирует ISO-8601 UTC, вернет нормализованную строку или false при ошибке.
     *
     * @param mixed $value
     */
    private function parseInstant($value, string $context, OutputInterface $output): string|false|null
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return Scalar::isoUtc($value, $context)->format(self::FORMAT);
        } catch (ImportException $e) {
            $output->getErrorOutput()->writeln($e->getMessage());
            return false;
        }
    }
}
