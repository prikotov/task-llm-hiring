<?php

declare(strict_types=1);

namespace App\Console\Command;

use App\Application\Billing\BillingReportBuilder;
use App\Application\Catalog\CatalogInput;
use App\Exception\DataImportException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'Отчёт расходов (CSV) по пользовательским расходам за период [from, to).',
)]
final class BillingReportCommand extends Command
{
    private const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    public function __construct(
        private readonly BillingReportBuilder $reportBuilder,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (ISO-8601, UTC), включительно');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (ISO-8601, UTC), исключительно');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $from = $this->parseBound($input->getOption('from'), '--from');
            $to = $this->parseBound($input->getOption('to'), '--to');
        } catch (DataImportException $e) {
            $output->getErrorOutput()->writeln('Ошибка: '.$e->getMessage());

            return Command::FAILURE;
        }

        $report = $this->reportBuilder->build($from, $to);


        $output->writeln(self::HEADER);
        foreach ($report->rows as $row) {
            $output->writeln(sprintf(
                '%s,%s,%d,%d,%d,%d,%s',
                $row->user,
                $row->model,
                $row->tokens[0],
                $row->tokens[1],
                $row->tokens[2],
                $row->tokens[3],
                $row->costUsd,
            ));
        }

        $output->writeln(sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $report->totalTokens[0],
            $report->totalTokens[1],
            $report->totalTokens[2],
            $report->totalTokens[3],
            $report->totalCostUsd,
        ));

        return Command::SUCCESS;
    }

    private function parseBound(mixed $value, string $option): ?\DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (!is_string($value)) {
            throw new DataImportException(sprintf('%s: expected an ISO-8601 UTC timestamp.', $option));
        }

        return CatalogInput::parseUtcTimestamp($value, $option);
    }
}
