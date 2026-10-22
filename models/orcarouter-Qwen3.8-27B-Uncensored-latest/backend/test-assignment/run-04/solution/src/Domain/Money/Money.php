<?php

declare(strict_types=1);

namespace App\Domain\Money;

use function bcadd;
use function bcsub;

/**
 * Money arithmetic based on exact decimal math (bcmath).
 *
 * All intermediate values are kept with {@see self::SCALE} decimal digits
 * (no rounding in between). float is never used.
 */
final class Money
{
    public const SCALE = 16;

    public const ONE_MILLION_STR = '1000000';

    private function __construct()
    {
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
        return \bcmul($a, $b, self::SCALE);
    }

    /**
     * Divides by a given exact decimal divisor (truncation towards zero; the
     * SCALE is large enough that no significant digit is ever truncated for
     * our operands).
     */
    public static function div(string $a, string $divisor): string
    {
        return \bcdiv($a, $divisor, self::SCALE);
    }

    /**
     * Single rounding of a non-negative decimal value to 6 digits after the
     * point, HALF_UP (0.0000005 -> 0.000001, 2.5 -> 3).
     *
     * Returns a string with exactly 6 digits after the point.
     */
    public static function roundHalfUp6(string $value): string
    {
        // micros = floor(value * 1e6 + 0.5); exact because value >= 0.
        $micros = bcadd(\bcmul($value, self::ONE_MILLION_STR, 1), '0.5', 0);

        return self::format6(\bcmul($micros, '0.000001', self::SCALE));
    }

    /**
     * Formats an exact decimal value (any sign) as a string with exactly 6
     * digits after the point. No rounding is performed — the input is assumed
     * to be exact at 6 digits (a sum of values already rounded to 6 digits).
     */
    public static function format6(string $value): string
    {
        $sign = '';
        if ($value[0] === '-' || \bccomp($value, '0', self::SCALE) < 0) {
            $sign = '-';
            $value = bcsub('0', $value, self::SCALE);
        }

        $value = bcadd($value, '0', self::SCALE); // normalize: int.frac with frac >= 1 digit

        [$int, $frac] = \explode('.', $value, 2);

        return $sign . $int . '.' . \substr($frac . '000000', 0, 6);
    }
}
