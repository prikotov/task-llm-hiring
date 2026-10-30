<?php

declare(strict_types=1);

namespace App\Application;

/**
 * HALF_UP rounding of a decimal string to a fixed number of places.
 *
 * Money is never a float, so the value's integer and fraction parts are handled
 * as strings. The rounding adds one to the integer formed by the kept digits
 * (integer part followed by the first $decimals fractional digits), which keeps
 * the decimal point in the right place. Correct for small magnitudes.
 */
final class DecimalRounding
{
    /** @param string $value decimal value as a string */
    public static function round(string $value, int $decimals): string
    {
        $value = self::normalize($value);
        $negative = str_starts_with($value, '-');
        $value = $negative ? substr($value, 1) : $value;

        $dot = strpos($value, '.');
        if (false === $dot) {
            $intPart = $value;
            $fracPart = '';
        } else {
            $intPart = substr($value, 0, $dot);
            $fracPart = substr($value, $dot + 1);
        }

        if ($decimals < 0) {
            $decimals = 0;
        }

        $keep = '0';
        $roundUp = false;
        if ('' !== $fracPart) {
            if (strlen($fracPart) > $decimals) {
                $keep = substr($fracPart, 0, $decimals);
                $next = (int) $fracPart[$decimals];
                $roundUp = $next >= 5;
            } else {
                $keep = $fracPart;
                $roundUp = false;
            }
        }

        $scaled = self::unpad($intPart . $keep);
        if ($roundUp) {
            $scaled = self::carryOne($scaled);
        }

        // Reinsert the decimal point $decimals digits from the right.
        $digits = self::unpad($scaled);
        while (strlen($digits) <= $decimals) {
            $digits = '0' . $digits;
        }
        if ($decimals > 0) {
            $int = substr($digits, 0, strlen($digits) - $decimals);
            $frac = substr($digits, strlen($digits) - $decimals);
        } else {
            $int = $digits;
            $frac = '';
        }

        return self::format($int, $frac, $decimals, $negative);
    }

    private static function normalize(string $value): string
    {
        $value = trim($value);
        if ('' === $value) {
            return '0';
        }

        if (str_contains($value, 'e') || str_contains($value, 'E')) {
            $value = rtrim(rtrim((string) sprintf('%.10f', (float) $value), '0'), '.');
        }

        return '' === $value ? '0' : $value;
    }

    private static function unpad(string $digits): string
    {
        $trimmed = ltrim($digits, '0');
        return '' === $trimmed ? '0' : $trimmed;
    }

    private static function carryOne(string $int): string
    {
        $int = self::unpad($int);
        $chars = array_reverse(str_split($int));
        $overflowed = true;
        foreach ($chars as $i => $ch) {
            if ('9' === $ch) {
                $chars[$i] = '0';
            } else {
                $chars[$i] = (string) ((int) $ch + 1);
                $overflowed = false;
                break;
            }
        }
        if ($overflowed) {
            array_unshift($chars, '1');
        }

        return self::unpad(implode('', array_reverse($chars)));
    }

    private static function format(string $int, string $frac, int $decimals, bool $negative): string
    {
        $w = '0';
        $unpadded = self::unpad($int);
        if ('' !== $unpadded) {
            $w = $unpadded;
        }
        if (0 >= $decimals) {
            return $w;
        }

        $s = $w . '.' . $frac;
        if (str_starts_with($s, '.')) {
            $s = '0' . $s;
        }

        return $negative ? '-' . $s : $s;
    }
}
