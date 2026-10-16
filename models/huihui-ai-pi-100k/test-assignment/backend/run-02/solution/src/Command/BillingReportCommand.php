<?php

declare(strict_types=1);

namespace App\Command;

use App\Domain\Iso8601;
use App\Domain\Money;
use App\Service\ReportService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Отчёт по рассчитанным событиям, сгруппирован по (user, model):
 * stdout — строгий CSV (UTF-8, `,`, LF) с заголовком и строкой TOTAL;
 * служебная информация — только в stderr.
 */
#[AsCommand(
    name: 'app:billing:report',
    description: 'Отчёт по рассчитанным событиям: суммы токенов и стоимости по (user, model)',
)]
final class BillingReportCommand extends Command
{
    public function __construct(
        private readonly ReportService $reportService,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('from', null, InputOption::VALUE_REQUIRED,
                'Нижняя граница по ts (включительно), ISO-8601 UTC')
            ->addOption('to', null, InputOption::VALUE_REQUIRED,
                'Верхняя граница по ts (исключительно), ISO-8601 UTC')
            ->setHelp(<<<'HELP'
                Примеры:
                  app:billing:report
                  app:billing:report --from 2026-09-01T00:00:00Z --to 2026-10-01T00:00:00Z

                В отчёт попадают только рассчитанные события (unrated — никогда).
                cost_usd — сумма уже округлённых до 6 знаков стоимостей событий.
                HELP);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $from = $this->parseBound('from', (string) $input->getOption('from'), $output);
        if ($from === false) {
            return Command::FAILURE;
        }

        $to = $this->parseBound('to', (string) $input->getOption('to'), $output);
        if ($to === false) {
            return Command::FAILURE;
        }

        try {
            $rows = $this->reportService->aggregate($from, $to);
        } catch (\RuntimeException $e) {
            $output->getErrorOutput()->writeln('Ошибка: ' . $e->getMessage());

            return Command::FAILURE;
        }

        $errorOutput = $output->getErrorOutput();
        if ($rows === []) {
            $errorOutput->writeln('Отчёт: рассчитанных событий за период не найдено (выдаётся пустой отчёт с TOTAL).');
        }

        $output->writeln('user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd');

        $totalInput = 0;
        $totalOutput = 0;
        $totalCacheRead = 0;
        $totalCacheWrite = 0;
        $totalCost = Money::zero();

        foreach ($rows as $row) {
            $totalInput += $row->tokensInput;
            $totalOutput += $row->tokensOutput;
            $totalCacheRead += $row->tokensCacheRead;
            $totalCacheWrite += $row->tokensCacheWrite;
            $totalCost = $totalCost->plus($row->cost);

            $output->writeln(implode(',', [
                $this->csv($row->user),
                $this->csv($row->modelDisplayName()),
                (string) $row->tokensInput,
                (string) $row->tokensOutput,
                (string) $row->tokensCacheRead,
                (string) $row->tokensCacheWrite,
                $row->cost->format(Money::US_DECIMALS),
            ]));
        }

        $totalLine = sprintf(
            'TOTAL,,%d,%d,%d,%d,%s',
            $totalInput,
            $totalOutput,
            $totalCacheRead,
            $totalCacheWrite,
            $totalCost->format(Money::US_DECIMALS),
        );
        $output->writeln($totalLine);

        return Command::SUCCESS;
    }

    /**
     * @return \DateTimeImmutable|false|null
     */
    private function parseBound(string $name, string $raw, OutputInterface $output): \DateTimeImmutable|false|null
    {
        if ($raw === '') {
            return null;
        }

        try {
            return Iso8601::parseUtc($raw);
        } catch (\ValueError $e) {
            $output->getErrorOutput()->writeln(sprintf('Ошибка: --%s: %s', $name, $e->getMessage()));

            return false;
        }
    }

    /** Экранирование значения CSV (поля из справочника — обычные строки). */
    private function csv(string $value): string
    {
        if (preg_match('/[",\n\r]/u', $value) === 1) {
            return '"' . addcslashes($value, '"') . '"';
        }

        return $value;
    }
}
