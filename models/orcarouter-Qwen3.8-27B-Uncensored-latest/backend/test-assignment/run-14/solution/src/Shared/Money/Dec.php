<?php

declare(strict_types=1);

namespace App\Shared\Money;

use LogicException;

/**
 * Decimal helpers. Rounding ({@see money()}) is implemented in pure string
 * arithmetic so it stays exact for arbitrarily long decimal tails.
 */
final class Dec
{
    /** Intermediate computation scale. */
    public const SCALE = 16;

    /** Reportable money decimals (TASK rule 4). */
    public const MONEY_SCALE = 6;

    /**
     * Validate and normalise to a plain decimal string.
     *
     * @throws LogicException when not a plain decimal.
     */
    public static function exact(string $value): string
    {
        if (preg_match('/^[+-]?(?:\d+(?:\.\d*)?|\.\d+)$/', $value) !== 1) {
            throw new LogicException(sprintf('Value "%s" is not a plain decimal number.', $value));
        }
        if (str_contains($value, '.')) {
            $value = (string) rtrim($value, '0');
            $value = (string) rtrim($value, '.');
        }
        if ($value === '' || $value === '+' || $value === '-') {
            return '0';
        }

        return $value;
    }

    /**
     * Round to exactly 6 decimals, HALF_UP (0.0000005 → 0.000001).
     */
    public static function money(string $value): string
    {
        return self::halfUp($value, self::MONEY_SCALE);
    }

    private static function halfUp(string $value, int $places): string
    {
        $value = self::exact($value);
        $negative = str_starts_with($value, '-');
        $body = ltrim($value, '+-');

        $dot = strpos($body, '.');
        $intPart = $dot === false ? $body : substr($body, 0, $dot);
        $fracPart = $dot === false ? '' : substr($body, $dot + 1);
        $intPart = ltrim($intPart, '0');
        if ($intPart === '') {
            $intPart = '0';
        }

        if (strlen($fracPart) <= $places) {
            $units = (string) ($intPart . str_pad($fracPart, $places, '0', STR_PAD_RIGHT));
            $carry = false;
        } else {
            $keep = substr($fracPart, 0, $places);
            $tail = substr($fracPart, $places);
            $units = (string) ($intPart . $keep);
            $carry = $tail[0] >= '5';
        }

        if ($carry) {
            $units = self::binc($units);
        }

        $units = ltrim($units, '0');
        if ($units === '') {
            $units = '0';
        }

        $formatted = strlen($units) <= $places
            ? '0.'.str_pad($units, $places, '0', STR_PAD_LEFT)
            : substr($units, 0, - $places) . '.' . substr($units, - $places);

        return $negative && $units !== '0' ? '-'.$formatted : $formatted;
    }

    private static function binc(string $digits): string
    {
        $out = '';
        $add = 1;
        for ($i = strlen($digits) - 1; $i >= 0; --$i) {
            $sum = (int) $digits[$i] + $add;
            $add = intdiv($sum, 10);
            $out = ($sum % 10) . $out;
        }
        if ($add !== 0) {
            $out = $add . $out;
        }

        return ltrim($out, '0') ?: '0';
    }
}
