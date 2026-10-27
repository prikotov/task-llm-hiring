<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Точная десятичная арифметика денег (bcmath).
 *
 * Все суммы хранятся строками; type float запрещён.
 * Промежуточные вычисления — с запасом точности (scale 16); float не используется.
 */
final class MoneyMath
{
    /** Запас точности для промежуточных вычислений. */
    public const SCALE = 16;

    private const DIVISOR_1M = '1000000';

    private function __construct()
    {
    }

    public static function zero(): string
    {
        return '0'.str_repeat('0', self::SCALE);
    }

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

    /**
     * Division с запасом точности (без округления результата).
     */
    public static function div(string $a, string $b): string
    {
        return bcdiv($a, $b, self::SCALE);
    }

    /**
     * Округление стоимости события до 6 знаков, HALF_UP (2.5 → 3).
     * Значения стоимости неотрицательны, поэтому классическая
     * «+0.5 и обрезать» формулировка корректна.
     */
    public static function roundHalfUpTo6(string $value): string
    {
        $micros = bcadd(bcmul($value, self::DIVISOR_1M, 16), '0.5', 0); // HALF_UP: +0.5 микрона и обрыв
        return bcdiv($micros, self::DIVISOR_1M, 6);
    }

    /**
     * Форматирование суммы ровно с 6 знаками после точки.
     * Задумано для сумм, кратных десяткам 6-го разряда (без усечения).
     */
    public static function formatTo6(string $value): string
    {
        $v = bcdiv($value, '1', 6);

        if (0 === \bccomp($v, '0', 6)) {
            $v = '0.000000';
        }

        return $v;
    }
}
