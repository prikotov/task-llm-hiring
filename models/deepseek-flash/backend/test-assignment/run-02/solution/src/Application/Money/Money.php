<?php

declare(strict_types=1);

namespace App\Application\Money;

use InvalidArgumentException;

/**
 * Точная десятичная арифметика для денег. Внутри используется bcmath,
 * float не применяется ни на одном шаге.
 */
final class Money
{
    public const CALC_SCALE = 24;
    public const MONEY_SCALE = 6;

    public static function add(string $left, string $right, int $scale = self::CALC_SCALE): string
    {
        return bcadd($left, $right, $scale);
    }

    public static function sub(string $left, string $right, int $scale = self::CALC_SCALE): string
    {
        return bcsub($left, $right, $scale);
    }

    public static function mul(string $left, string $right, int $scale = self::CALC_SCALE): string
    {
        return bcmul($left, $right, $scale);
    }

    public static function div(string $left, string $right, int $scale = self::CALC_SCALE): string
    {
        if (bccomp($right, '0', $scale) === 0) {
            throw new InvalidArgumentException('Division by zero.');
        }

        return bcdiv($left, $right, $scale);
    }

    /**
     * Округление HALF_UP (2.5 -> 3, -2.5 -> -3).
     */
    public static function roundHalfUp(string $value, int $scale = self::MONEY_SCALE): string
    {
        $quantum = '0.'.str_repeat('0', $scale).'5';

        $rounded = str_starts_with($value, '-')
            ? bcsub($value, $quantum, $scale)
            : bcadd($value, $quantum, $scale);

        return self::format($rounded, $scale);
    }

    public static function format(string $value, int $scale = self::MONEY_SCALE): string
    {
        return bcadd($value, '0', $scale);
    }

    public static function isDecimal(string $value): bool
    {
        return (bool) preg_match('/^-?\d+(?:\.\d+)?$/', $value);
    }
}
