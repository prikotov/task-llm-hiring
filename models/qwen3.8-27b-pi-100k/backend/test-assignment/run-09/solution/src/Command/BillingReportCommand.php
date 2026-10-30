<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Billing\DecimalMath;
use App\Domain\Billing\IsoDateTime;
use App\Service\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'Отчёт по расходам (CSV в stdout): рассчитанные события за период, сгруппированы по user/model.',
)]
final class BillingReportCommand extends Command
{
    public function __construct(private readonly ReportService $reportService)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (ISO-8601, включительно). Без опции — от всех событий.');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (ISO-8601, исключительно). Без опции — без верхнего ограничения.');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $err = $output->getErrorOutput();

        try {
            $from = $this->optionalBound($input, 'from');
            $to = $this->optionalBound($input, 'to');
        } catch (\Throwable $e) {
            $err->writeln(sprintf('Некорректный диапазон периода: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        try {
            $report = $this->reportService->report($from, $to);
        } catch (\Throwable $e) {
            $err->writeln(sprintf('Ошибка формирования отчёта: %s', $e->getMessage()));

            return Command::FAILURE;
        }

        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');

        foreach ($report['rows'] as $row) {
            $output->writeln(implode(',', [
                (string) $row['user'],
                (string) $row['model'],
                (string) $row['tokens_input'],
                (string) $row['tokens_output'],
                (string) $row['tokens_cache_read'],
                (string) $row['tokens_cache_write'],
                DecimalMath::format6((string) $row['cost']),
            ]));
        }

        $t = $report['total'];
        $output->writeln(implode(',', [
            'TOTAL',
            '',
            (string) $t['tokens_input'],
            (string) $t['tokens_output'],
            (string) $t['tokens_cache_read'],
            (string) $t['tokens_cache_write'],
            DecimalMath::format6((string) $t['cost']),
        ]));

        return Command::SUCCESS;
    }

    private function optionalBound(InputInterface $input, string $name): ?\DateTimeImmutable
    {
        $value = $input->getOption($name);

        if (null === $value) {
            return null;
        }

        return IsoDateTime::parseUtc((string) $value);
    }
}
