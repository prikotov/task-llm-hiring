<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Рендер CSV-отчёта (UTF-8, разделитель «,», перевод строки «\n»).
 *
 * user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
 *
 * Поля экранируются, только если содержат запятую/кавычку/перевод строки
 * (для данных из примера задание — вывод совпадает байт-в-байт).
 */
final class ReportCsv
{
    public const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    /**
     * @param list<array{user: string, model: string, tokens: list<int>, cost: Money}> $rows
     *      отсортированные по (user, model) группы событий
     * @param list<int> $totalTokens [input, output, cache_read, cache_write] по всем группам
     * @param Money $totalCost сумма округлённых стоимостей событий отчёта
     */
    public static function render(array $rows, array $totalTokens, Money $totalCost): string
    {
        $lines = [self::HEADER];

        foreach ($rows as $row) {
            $t = $row['tokens'];
            $lines[] = implode(',', [
                self::field($row['user']),
                self::field($row['model']),
                (string) $t[0],
                (string) $t[1],
                (string) $t[2],
                (string) $t[3],
                $row['cost']->format(6),
            ]);
        }

        $lines[] = implode(',', [
            'TOTAL',
            '',
            (string) $totalTokens[0],
            (string) $totalTokens[1],
            (string) $totalTokens[2],
            (string) $totalTokens[3],
            $totalCost->format(6),
        ]);

        return implode("\n", $lines) . "\n";
    }

    private static function field(string $value): string
    {
        if (preg_match('/[",\r\n]/', $value) === 1) {
            return '"' . str_replace('"', '""', $value) . '"';
        }

        return $value;
    }
}
