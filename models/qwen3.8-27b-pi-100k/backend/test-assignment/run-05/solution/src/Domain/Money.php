<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Точная десятичная арифметика для денег (bcmath, без float).
 *
 * Рабочий масштаб — 16 знаков (запас точности для промежуточных значений);
 * единственное округление стоимости — round6() (HALF_UP, 6 знаков).
 */
final class Money
{
    public const SCALE = 16;

    private function __construct()
    {
    }

    public static function add(string $a, string $b): string
    {
        return bcadd($a, $b, self::SCALE);
    }

    public static function mul(string $a, string $b): string
    {
        return bcmul($a, $b, self::SCALE);
    }

    /** Округление HALF_UP до 6 знаков (x >= 0: floor(x*1e6 + 0.5)/1e6; знак отрицательных сохраняется). */
    public static function round6(string $amount): string
    {
        $negative = bccomp($amount, '0', self::SCALE) < 0;
        $abs = $negative ? ltrim($amount, '-') : $amount;

        $scaled = bcmul($abs, '1000000', self::SCALE);
        $int = bcdiv(bcadd($scaled, '0.5', 1), '1', 0); // отбрасывание = floor для неотрицательных

        $result = bcdiv($int, '1000000', 6);

        return $negative && bccomp($result, '0', 6) > 0 ? '-'.$result : $result;
    }

    /** Форматирование ровно с 6 знаками после точки (без изменения значения). */
    public static function format6(string $amount): string
    {
        return bcadd($amount, '0', 6);
    }
}
