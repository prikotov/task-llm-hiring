<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Dto\ReportRow;
use App\Application\ReportService;
use App\Domain\Billing\Iso8601;
use App\Domain\Exception\BillingException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'Вывести CSV-отчёт расходов по пользователям и моделям за период (stdout)',
)]
final class ReportCommand extends Command
{
    public function __construct(private readonly ReportService $reports)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода, ISO-8601 в UTC; включительно')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода, ISO-8601 в UTC; исключительно');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = self::parseUtcBound($input->getOption('from'), '--from');
            $to = self::parseUtcBound($input->getOption('to'), '--to');
            $report = $this->reports->build($from, $to);
        } catch (BillingException $exception) {
            $output->getErrorOutput()->writeln('Ошибка: '.$exception->getMessage());
            return 1;
        }

        // CSV только в stdout — stdout полностью парсится как CSV.
        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');
        foreach ($report->rows as $row) {
            $output->writeln(self::renderCsv($row));
        }
        $output->writeln(self::renderCsv($report->total));

        return 0;
    }

    private static function renderCsv(ReportRow $row): string
    {
        return implode(',', [
            $row->user,
            $row->model,
            (string) $row->tokensInput,
            (string) $row->tokensOutput,
            (string) $row->tokensCacheRead,
            (string) $row->tokensCacheWrite,
            $row->costUsd,
        ]);
    }

    private static function parseUtcBound(mixed $value, string $option): ?\DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }
        if (!\is_string($value)) {
            throw new BillingException(sprintf('Значение %s должно быть строкой ISO-8601', $option));
        }

        return Iso8601::parseUtc($value);
    }
}
