<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Точная десятичная арифметика на bcmath.
 *
 * Все денежные величины оперируются строками; float не используется.
 * Промежуточные вычисления без округления (рабочий масштаб >= 16),
 * финальное округление — одно, HALF_UP, до 6 знаков после точки.
 */
final class DecimalMath
{
    /** Рабочий масштаб промежуточных вычислений. */
    public const SCALE = 16;

    /** Число знаков после точки в итоговой стоимости события. */
    public const COST_SCALE = 6;

    /** Делитель: цена задана за 1 000 000 токенов. */
    private const TOKENS_UNIT = '1000000';

    /**
     * Округление HALF_UP (2.5 -> 3) до $scale знаков после точки.
     * Положительные и отрицательные значения округляются от нулевого
     * по модулю; результат всегда содержит ровно $scale знаков дробной части.
     */
    public static function roundHalfUp(string $value, int $scale = self::COST_SCALE): string
    {
        $negative = str_starts_with($value, '-');
        $absolute = $negative ? substr($value, 1) : $value;

        // Именно $scale + 1 знаков дробной части (hвесь хвост отбрасывается).
        $absolute = bcadd($absolute, '0', $scale + 1);
        $boundaryDigit = (int) substr($absolute, -1); // первый отброшенный знак
        $base = substr($absolute, 0, -1);             // ровно $scale знаков дробной части
        $rounded = $boundaryDigit >= 5 ? bcadd($base, self::unitOne($scale), $scale) : $base;

        if (self::isZero($rounded)) {
            return '0.'.str_repeat('0', $scale);
        }

        return ($negative ? '-' : '').$rounded;
    }

    /** Является ли десятичная строка нулём (bccomp в этой сборке PHP сломан — строчное сравнение). */
    public static function isZero(string $value): bool
    {
        return ltrim(str_replace(['+', '-', '.'], '', $value), '0') === '';
    }

    /**
     * tokens * rate / 1 000 000 — точное значение без финального округления.
     */
    public static function tokenCost(int $tokens, string $rate): string
    {
        if ($tokens <= 0 || self::isZero($rate)) {
            return '0';
        }

        return bcdiv(bcmul((string) $tokens, $rate, self::SCALE), self::TOKENS_UNIT, self::SCALE);
    }

    private static function unitOne(int $scale): string
    {
        return '0.'.str_repeat('0', $scale - 1).'1';
    }
}
