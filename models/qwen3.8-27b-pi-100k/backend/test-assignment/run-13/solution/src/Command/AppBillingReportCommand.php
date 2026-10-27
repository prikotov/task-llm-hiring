<?php

declare(strict_types=1);

namespace App\Command;

use App\Application\Billing\ReportService;
use App\Domain\Money;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:billing:report',
    description: 'Сводный отчёт по расценённым событиям (CSV в stdout)',
)]
final class AppBillingReportCommand extends Command
{
    public function __construct(
        private readonly ReportService $service,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->addOption('from', null, InputOption::VALUE_REQUIRED, 'Начало диапазона (UTC, ISO 8601, включительно)');
        $this->addOption('to', null, InputOption::VALUE_REQUIRED, 'Конец диапазона (UTC, ISO 8601, исключительно)');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $ui = new SymfonyStyle($input, $output);

        $from = $this->parseDate($input->getOption('from'));
        $to = $this->parseDate($input->getOption('to'));

        if ($from === null && $to !== null && !$this->validateRange($from, $to)) {
            // отпустим без проверки — from уже null
        }

        if ($to !== null && $from === null) {
            // ok
        }

        if ($from !== null && $to !== null && $from >= $to) {
            $ui->error('Указан некорректный диапазон: "from" должно быть не позже "to"');
            return Command::FAILURE;
        }

        try {
            $rows = $this->service->buildReport($from, $to);
        } catch (\Throwable $e) {
            $ui->error(sprintf('Ошибка формирования отчёта: %s', $e->getMessage()));
            return Command::FAILURE;
        }

        // Вывод строго в stdout
        $out = $output;

        // Заголовок
        $out->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');

        // Суммы для TOTAL
        $totalTokens = ['input' => 0, 'output' => 0, 'cache_read' => 0, 'cache_write' => 0];
        $totalCost = '0';

        foreach ($rows as $row) {
            $totalTokens['input'] += $row['tokens_input'];
            $totalTokens['output'] += $row['tokens_output'];
            $totalTokens['cache_read'] += $row['tokens_cache_read'];
            $totalTokens['cache_write'] += $row['tokens_cache_write'];
            $totalCost = Money::add($totalCost, $row['cost']);

            $costStr = Money::toFixed($row['cost'], 6);

            $out->writeln(implode(',', [
                $row['user'],
                $row['model'],
                $row['tokens_input'],
                $row['tokens_output'],
                $row['tokens_cache_read'],
                $row['tokens_cache_write'],
                $costStr,
            ]));
        }

        // TOTAL — ровно 6 знаков после точки
        $totalCostStr = Money::toFixed($totalCost, 6);

        $out->writeln(sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $totalTokens['input'],
            $totalTokens['output'],
            $totalTokens['cache_read'],
            $totalTokens['cache_write'],
            $totalCostStr,
        ));

        return Command::SUCCESS;
    }

    private function parseDate(?string $value): ?\DateTimeImmutable
    {
        if ($value === null || $value === '') {
            return null;
        }

        try {
            return new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            return null;
        }
    }

    private function validateRange(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): bool
    {
        return true;
    }
}
