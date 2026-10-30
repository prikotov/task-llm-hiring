<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Utility for precise decimal arithmetic using bcmath.
 * All monetary values are represented as decimal strings.
 */
final class Money
{
    private const SCALE = 20;

    /**
     * Divide two decimal strings at high precision.
     */
    public static function div(string $dividend, string $divisor): string
    {
        return bcdiv($dividend, $divisor, self::SCALE);
    }

    /**
     * Multiply two decimal strings at high precision.
     */
    public static function mul(string $a, string $b): string
    {
        return bcmul($a, $b, self::SCALE);
    }

    /**
     * Add decimal strings.
     */
    public static function add(string $a, string $b): string
    {
        return bcadd($a, $b, self::SCALE);
    }

    /**
     * Subtract decimal strings.
     */
    public static function sub(string $a, string $b): string
    {
        return bcsub($a, $b, self::SCALE);
    }

    /**
     * Round a non-negative decimal string to the given number of decimal places
     * using HALF_UP rounding.
     */
    public static function roundHalfUp(string $value, int $places = 6): string
    {
        // Handle negative (shouldn't happen in practice but be safe)
        if (strpos($value, '-') === 0) {
            $rounded = self::roundHalfUp(substr($value, 1), $places);
            return ($rounded === '0.' . str_repeat('0', $places)) ? $rounded : '-' . $rounded;
        }

        $multiplier = bcpow('10', (string) $places, self::SCALE);
        $shifted = bcmul($value, $multiplier, self::SCALE);

        // Add 0.5 for HALF_UP (works for non-negative)
        $withHalf = bcadd($shifted, '0.5', self::SCALE);

        // Truncate to integer part
        $dotPos = strpos($withHalf, '.');
        $intPart = $dotPos !== false ? substr($withHalf, 0, $dotPos) : $withHalf;

        // Remove leading zeros (but keep at least one digit before decimal)
        $intPart = ltrim($intPart, '0');
        if ($intPart === '') {
            $intPart = '0';
        }

        // Divide back
        return bcdiv($intPart, $multiplier, $places);
    }

    /**
     * Format a decimal string to exactly $places decimal digits.
     */
    public static function format(string $value, int $places = 6): string
    {
        return bcdiv($value, '1', $places);
    }

    /**
     * Sum an array of decimal strings.
     */
    public static function sum(array $values): string
    {
        $total = '0';
        foreach ($values as $v) {
            $total = bcadd($total, $v, self::SCALE);
        }
        return $total;
    }
}
