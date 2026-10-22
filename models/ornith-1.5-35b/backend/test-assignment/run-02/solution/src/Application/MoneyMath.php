<?php

declare(strict_types=1);

namespace App\Application;

/**
 * Decimal-money arithmetic helper using bcmath.
 *
 * Money is always represented as a string (fixed point). Floats (double) are
 * never used for money so that there are no floating-point rounding artefacts.
 */
final class MoneyMath
{
    public const int WORKING_SCALE = 16; // precision for intermediate calculations
    public const int RESULT_SCALE = 6;   // precision for final / reported amounts

    public static function add(string $a, string $b): string
    {
        return bcadd($a, $b, self::WORKING_SCALE);
    }

    public static function multiply(string $a, string $b): string
    {
        return bcmul($a, $b, self::WORKING_SCALE);
    }

    public static function divide(string $a, string $b): string
    {
        if (bccomp($b, '0', self::WORKING_SCALE) === 0) {
            return '0';
        }

        return bcdiv($a, $b, self::WORKING_SCALE);
    }

    /**
     * Round a value to 6 decimal places using HALF_UP.
     */
    public static function round6(string $value): string
    {
        return DecimalRounding::round($value, self::RESULT_SCALE);
    }

    /**
     * Sum a list of values at working precision, returning an exact (unrounded) decimal.
     *
     * @param array<int, string> $values
     */
    public static function sumMany(array $values): string
    {
        $result = '0';
        foreach ($values as $value) {
            if ('' !== $value && null !== $value) {
                $result = bcadd($result, (string) $value, self::WORKING_SCALE);
            }
        }

        return $result;
    }

    /**
     * Format a computed decimal as a 6-decimal report string (HALF_UP rounding).
     */
    public static function format6(string $value): string
    {
        return DecimalRounding::round($value, self::RESULT_SCALE);
    }
}
