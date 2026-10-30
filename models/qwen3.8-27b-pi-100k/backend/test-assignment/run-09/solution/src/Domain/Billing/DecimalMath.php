<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use function str_starts_with;

/**
 * Низкоуровневая десятичная арифметика на bcmath.
 *
 * float для денег запрещён: все операции строчные, масштаб заведомо
 * избыточный (>= 16 по ТЗ), чтобы промежуточные значения не округлялись.
 *
 * @internal
 */
final class DecimalMath
{
    /** Рабочий масштаб промежуточных вычислений (запас точности). */
    public const SCALE = 20;

    /** Число знаков после запятой у итоговой стоимости денег. */
    public const MONEY_PLACES = 6;

    /** Делитель: цена задана за 1 000 000 токенов. */
    public const TOKEN_DIVISOR = '1000000';

    public static function mul(string $a, string $b): string
    {
        return bcmul($a, $b, self::SCALE);
    }

    public static function div(string $a, string $b): string
    {
        return bcdiv($a, $b, self::SCALE);
    }

    public static function add(string $a, string $b): string
    {
        return bcadd($a, $b, self::SCALE);
    }

    public static function sub(string $a, string $b): string
    {
        return bcsub($a, $b, self::SCALE);
    }

    public static function compare(string $a, string $b): int
    {
        return bccomp($a, $b, self::SCALE);
    }

    /**
     * Округление HALF_UP до 6 знаков после точки (половина — от нуля).
     *
     * Работает только со строками, float не задействован.
     */
    public static function halfUp6(string $value): string
    {
        $value = ltrim($value, '+');
        $negative = str_starts_with($value, '-');
        if ($negative) {
            $value = substr($value, 1);
        }
        if ($value === '') {
            $value = '0';
        }

        // scaled = value * 10^6, затем +0.5 и усечение -> floor(value*10^6 + 0.5)
        $scaled = bcadd(bcmul($value, '1000000', self::SCALE), '0.5', self::SCALE);
        $integerPart = self::floor($scaled);

        $whole = bcdiv($integerPart, '1000000', 0);
        $remainder = bcmod($integerPart, '1000000');

        return ($negative ? '-' : '') . $whole . '.' . str_pad($remainder, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Приводит строковое десятичное значение к ровно 6 знакам после точки
     * (без округления — только фиксация ширины; входные значения уже не
     * грубее 6 знаков, поэтому операция точна).
     */
    public static function format6(string $value): string
    {
        $value = ltrim($value, '+');
        $negative = str_starts_with($value, '-');
        if ($negative) {
            $value = substr($value, 1);
        }
        if ($value === '') {
            $value = '0';
        }

        if (!str_contains($value, '.')) {
            return ($negative ? '-' : '') . $value . '.000000';
        }

        [$int, $frac] = explode('.', $value, 2);
        if ($int === '') {
            $int = '0';
        }
        $frac = substr(str_pad($frac, 6, '0'), 0, 6);

        return ($negative ? '-' : '') . $int . '.' . $frac;
    }

    private static function floor(string $value): string
    {
        $parts = explode('.', $value, 2);

        return ltrim($parts[0] ?? '0', '+') ?: '0';
    }
}
