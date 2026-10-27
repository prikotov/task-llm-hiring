<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Точная десятичная арифметика для денег (bcmath).
 *
 * Все операции выполняются с запасом точности SCALE (16 знаков),
 * промежуточные значения не округляются. Float для денег не используется.
 */
final class MoneyMath
{
    public const SCALE = 16;

    public static function add(string $a, string $b): string
    {
        return bcadd($a, $b, self::SCALE);
    }

    public static function sub(string $a, string $b): string
    {
        return bcsub($a, $b, self::SCALE);
    }

    public static function mul(string $a, string $b): string
    {
        return bcmul($a, $b, self::SCALE);
    }

    public static function div(string $a, string $b): string
    {
        return bcdiv($a, $b, self::SCALE);
    }

    /** Округление неотрицательного значения до 6 знаков после точки, правило HALF_UP. */
    public static function roundHalfUp6(string $value): string
    {
        $shifted = bcmul($value, '1000000', 0); // trunc: $value >= 0
        $fraction = bcsub(bcmul($value, '1000000', 6), $shifted, 6);

        if (bccomp($fraction, '0.5', 6) >= 0) {
            $shifted = bcadd($shifted, '1', 0);
        }

        return bcdiv($shifted, '1000000', 6);
    }

    /** Приводит значение к формату ровно с 6 знаками после точки. */
    public static function format6(string $value): string
    {
        $normalized = bcdiv($value, '1', 6);

        // bc может дать "0.000000" и "-0.000000"; нулевую отрицательную форму приводим к положительной.
        if (bccomp($normalized, '0', 6) === 0) {
            return '0.000000';
        }

        return $normalized;
    }
}
