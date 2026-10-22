<?php

declare(strict_types=1);

namespace App\Billing\Application\Reporting;

use App\Billing\Domain\Money\Money;

/**
 * Формат CSV-отчёта (TASK: «CSV в stdout, UTF-8, разделитель ',' , \n»).
 * Форматирование отделено от сборки данных: байт-в-байт совпадение с
 * примером — требование приёмки, его проще проверять в одном месте.
 */
final class CsvReportFormatter
{
    public const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    public function format(Report $report): string
    {
        $lines = [self::HEADER];

        foreach ($report->rows as $row) {
            $lines[] = $this->line($row->user, $row->model(), $row);
        }

        $lines[] = $this->line('TOTAL', '', $report->total);

        return implode("\n", $lines)."\n";
    }

    private function line(string $user, string $model, ReportGroupRow $row): string
    {
        return implode(',', [
            $this->escape($user),
            $this->escape($model),
            (string) $row->tokensInput,
            (string) $row->tokensOutput,
            (string) $row->tokensCacheRead,
            (string) $row->tokensCacheWrite,
            $row->cost->roundHalfUp(Money::MONEY_SCALE)->toString(),
        ]);
    }

    /**
     * Пользователь — свободная строка, поэтому значения с запятой, кавычкой
     * или переводом строки экранируются по RFC 4180, чтобы CSV остался валидным.
     */
    private function escape(string $value): string
    {
        if (1 === preg_match('/[",\n\r]/', $value)) {
            return '"'.str_replace('"', '""', $value).'"';
        }

        return $value;
    }
}
