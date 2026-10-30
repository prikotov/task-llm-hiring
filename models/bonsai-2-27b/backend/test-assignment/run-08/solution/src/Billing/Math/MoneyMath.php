<?php

declare(strict_types=1);

namespace App\Billing\Math;

/**
 * Точная десятичная арифметика на bcmath: float для денег запрещён.
 * Масштаб промежуточных вычислений — 16 знаков (требование ≥ 16).
 */
final class MoneyMath
{
    public const SCALE = 16;

    /** Разность двух десятичных строк. */
    public static function sub(string $a, string $b): string
    {
        return bcsub($a, $b, 0);
    }

    /**
     * Округление HALF_UP до 6 знаков (bcround с round=1).
     */
    public static function roundHalfUp6(string $value): string
    {
        return (string) bcround($value, 6, 1);
    }

    /** Сопоставление строк численно. */
    public static function cmp(string $a, string $b): int
    {
        return bccomp($a, $b, 0);
    }

    /** Перемножение. */
    public static function mul(string $a, string $b): string
    {
        return bcmul($a, $b, self::SCALE);
    }

    /** Деление. */
    public static function div(string $a, string $b): string
    {
        return bcdiv($a, $b, self::SCALE);
    }

    /** Формат: ровно 6 знаков после точки (для вывода). */
    public static function format6(string $value): string
    {
        // Если уже 6 знаков — как есть; если меньше — дописать нули.
        $parts = explode('.', $value);
        $integerPart = $parts[0];
        $fractionPart = $parts[1] ?? '0';
        while (strlen($fractionPart) < 6) {
            $fractionPart .= '0';
        }
        if (strlen($fractionPart) > 6) {
            // Не должно происходить после roundHalfUp6.
            $fractionPart = substr($fractionPart, 0, 6);
        }
        return $integerPart . '.' . $fractionPart;
    }
}
