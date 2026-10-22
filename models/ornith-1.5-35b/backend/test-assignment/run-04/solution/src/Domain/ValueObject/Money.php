<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

/**
 * Точная десятичная арифметика через bcmath. Float для денег запрещён.
 *
 * Промежуточные вычисления — с масштабом 16. Итоговое округление до 6 знаков
 * по правилу HALF_UP (½ в сторону от нуля).
 */
final class Money
{
    public const SCALE = 16;
    public const ROUND_SCALE = 6;

    public static function add(string ...$values): string
    {
        $sum = '0';
        foreach ($values as $v) {
            if (null === $v || '' === $v) {
                continue;
            }
            $sum = bcadd($sum, $v, self::SCALE);
        }

        return $sum;
    }

    /**
     * cost = tokens × rate / 1_000_000, без округления.
     */
    public static function cost(int $tokens, string $rate): string
    {
        if (0 === $tokens) {
            return '0';
        }

        $product = bcmul((string) $tokens, $rate, self::SCALE);

        return bcdiv($product, '1000000', self::SCALE);
    }

    public static function multiply(string $value, string $multiplier): string
    {
        return bcmul($value, $multiplier, self::SCALE);
    }

    public static function diff(string $new, string $old): string
    {
        return bcsub($new, $old, self::SCALE);
    }

    /**
     * Округление до ROUND_SCALE знаков по правилу HALF_UP (½ в сторону от
     * нуля). Деньги неотрицательны, поэтому HALF_UP = обычное округление в
     * сторону большего модуля. Возвращает строку с ровно ROUND_SCALE знаками
     * после точки.
     */
    public static function round(string $value): string
    {
        [$int, $frac] = splitDecimal($value);

        $keep = substr($frac, 0, self::ROUND_SCALE);
        if (self::ROUND_SCALE < strlen($keep)) {
            $keep = substr($keep, 0, self::ROUND_SCALE);
        }
        $keep = str_pad($keep, self::ROUND_SCALE, '0');

        $marker = (int) ($frac[self::ROUND_SCALE] ?? '0');
        if ($marker >= 5) {
            $incr = increment($keep);
            if (strlen($incr) > self::ROUND_SCALE) {
                // перенос в целую часть (переполнение шестизначного kept)
                $carry = substr($incr, 0, -self::ROUND_SCALE);
                $keep = substr($incr, -self::ROUND_SCALE);
                $int = (string) ((int) $int + (int) $carry);
            } else {
                $keep = $incr;
            }
        }

        return $int . '.' . $keep;
    }
}

/**
 * Разложение числа на целую и дробную части (без float).
 *
 * @return array{0:string,1:string}
 */
function splitDecimal(string $value): array
{
    if ('' === $value) {
        return ['0', ''];
    }
    $neg = str_starts_with($value, '-');
    if ($neg) {
        $value = substr($value, 1);
    }
    $dot = strpos($value, '.');
    $int = $dot === false ? $value : substr($value, 0, $dot);
    $frac = $dot === false ? '' : substr($value, $dot + 1);
    $int = ltrim($int, '0') ?: '0';
    if ($neg) {
        // знаки не меняем (money >= 0), держим формальную поддержку
        $int = '-' . $int;
    }

    return [$int, $frac];
}

/**
 * Инкремент строки из десятичных цифр на единицу. Целая часть не участвует.
 */
function increment(string $digits): string
{
    $chars = str_split($digits);
    $i = count($chars) - 1;
    while ($i >= 0) {
        if ('9' === $chars[$i]) {
            $chars[$i] = '0';
            --$i;
        } else {
            $chars[$i] = (string) ((int) $chars[$i] + 1);

            return implode('', $chars);
        }
    }

    // все девятки: перенос за пределы (например 999999 -> 1000000)
    return '1' . implode('', $chars);
}
