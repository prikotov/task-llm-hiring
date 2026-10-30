<?php

declare(strict_types=1);

namespace App\Report;

/**
 * CSV-отчёт: разделитель ",", перевод строки "\n", UTF-8.
 * Формат:
 *
 *   user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd
 *   u-alice,prism/lumen-8,...,14.262499
 *   TOTAL,,6200000,2300000,2500000,83333,33.662499
 *
 * cost_usd — ровно 6 знаков после точки; токены — целые без разделителей;
 * TOTAL — пустое поле model и суммарные значения по отчёту.
 */
final class ReportCsv
{
    public const HEADER = 'user,model,tokens_input,tokens_output,tokens_cache_read,tokens_cache_write,cost_usd';

    /**
     * @param array<int, array{user: string, model: string, tokens: array<string, string>, cost: string}> $groups
     * @param array{tokens: array<string, string>, cost: string} $total
     */
    public static function render(array $groups, array $total): string
    {
        $lines = [self::HEADER];

        foreach ($groups as $group) {
            $lines[] = implode(',', [
                self::field($group['user']),
                self::field($group['model']),
                self::integer($group['tokens']['tokens_input']),
                self::integer($group['tokens']['tokens_output']),
                self::integer($group['tokens']['tokens_cache_read']),
                self::integer($group['tokens']['tokens_cache_write']),
                self::money($group['cost']),
            ]);
        }

        $lines[] = 'TOTAL,,' . implode(',', [
            self::integer($total['tokens']['tokens_input']),
            self::integer($total['tokens']['tokens_output']),
            self::integer($total['tokens']['tokens_cache_read']),
            self::integer($total['tokens']['tokens_cache_write']),
            self::money($total['cost']),
        ]);

        return implode("\n", $lines) . "\n";
    }

    /** Интегральная величина без разделителей (отбрасываем дробную часть, если появилась). */
    private static function integer(string $value): string
    {
        $padded = bcadd($value, '0', 6);
        [, , $intPart] = self::split($padded);

        return ltrim($intPart, '0') === '' ? '0' : ltrim($intPart, '0');
    }

    private static function money(string $value): string
    {
        $sign = str_starts_with($value, '-') ? '-' : '';
        $body = $sign === '-' ? substr($value, 1) : $value;
        $padded = bcadd($body, '0', 6);
        [, , $intPart] = self::split($padded);

        return $sign . (ltrim($intPart, '0') === '' ? '0' : ltrim($intPart, '0')) . '.' . substr($padded, -6);
    }

    /**
     * @return array{0: string, 1: string, 2: string} [знак, дробная часть (6), целая часть]
     */
    private static function split(string $padded): array
    {
        $sign = str_starts_with($padded, '-') ? '-' : '';
        $body = $sign === '-' ? substr($padded, 1) : $padded;

        $dot = strrpos($body, '.');
        if ($dot === false) {
            return [$sign, str_repeat('0', 6), $body];
        }

        return [$sign, substr($body, $dot + 1), substr($body, 0, $dot)];
    }

    /** Экранирование по RFC 4180, если значение содержит специальные символы. */
    private static function field(string $value): string
    {
        if (preg_match('/^[^",\r\n]*$/', $value) === 1) {
            return $value;
        }

        return '"' . str_replace('"', '""', $value) . '"';
    }
}
