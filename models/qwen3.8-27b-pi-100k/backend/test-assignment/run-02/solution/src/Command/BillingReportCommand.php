<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\ReportService;
use App\Support\StrictIsoUtc;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'app:billing:report',
    description: 'CSV-отчёт по рассчитанным событиям: user,model,tokens…,cost_usd (stdout)',
)]
final class BillingReportCommand extends AbstractBillingCommand
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало периода (ISO-8601, UTC, включительно), по ts события')
            ->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец периода (ISO-8601, UTC, исключительно), по ts события')
            ->setHelp('CSV уходит в stdout (UTF-8, разделитель ",", перенос \n). Ошибки — в stderr, код 1. Unrated-события не попадают в отчёт.');
    }

    protected function doExecute(InputInterface $input, OutputInterface $errorOut): int
    {
        $from = $this->optionalDate($input->getOption('from'), '--from');
        $to = $this->optionalDate($input->getOption('to'), '--to');

        $report = (new ReportService($this->em))->report($from, $to);

        // CSV строго в stdout (Пункт «Приёмка»: байт-в-байт совпадение с примером).
        $lines = ['user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd'];
        foreach ($report->rows as $row) {
            $lines[] = implode(',', [
                $row['user'],
                $row['model'],
                (string) $row['tokens']['input'],
                (string) $row['tokens']['output'],
                (string) $row['tokens']['cache_read'],
                (string) $row['tokens']['cache_write'],
                $row['cost'],
            ]);
        }
        $lines[] = implode(',', [
            'TOTAL',
            '',
            (string) $report->totalTokens['input'],
            (string) $report->totalTokens['output'],
            (string) $report->totalTokens['cache_read'],
            (string) $report->totalTokens['cache_write'],
            $report->totalCost,
        ]);

        fwrite(STDOUT, implode("\n", $lines)."\n");

        return 0;
    }

    private function optionalDate(mixed $value, string $option): ?\DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        return StrictIsoUtc::parse((string) $value);
    }
}
