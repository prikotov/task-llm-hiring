<?php

declare(strict_types=1);

namespace App\Domain\Billing\Util;

use function bcmul;
use function bcsub;
use function bcadd;
use function bcdiv;

/**
 * Утилиты точной десятичной арифметики на bcmath.
 *
 * Все внутренние промежуточные значения хранятся со SCALE знаков после
 * точки (без округления), итоговое округление до 6 знаков (HALF_UP)
 * выполняется отдельно вызовом roundHalfUp(). Float для денег не
 * используется.
 */
final class Money
{
    /** Запас точности промежуточных вычислений. */
    public const SCALE = 20;

    private const TOKEN_UNIT = '1000000';

    private function __construct()
    {
    }

    public static function add(string ...$summands): string
    {
        $result = '0';
        foreach ($summands as $summand) {
            $result = bcadd($result, self::normalize($summand), self::SCALE);
        }

        return self::clean($result);
    }

    public static function sub(string $a, string $b = '0'): string
    {
        return self::clean(bcsub(self::normalize($a), self::normalize($b), self::SCALE));
    }

    public static function mul(string $a, string $b): string
    {
        return self::clean(bcmul(self::normalize($a), self::normalize($b), self::SCALE));
    }

    public static function div(string $a, string $b): string
    {
        return self::clean(bcdiv(self::normalize($a), self::normalize($b), self::SCALE));
    }

    /** Стоимость по схеме per_1m_tokens: Σ token_вид × rate_вид / 1 000 000. */
    public static function perTokenCost(
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        string $rateInput,
        string $rateOutput,
        string $rateCacheRead,
        string $rateCacheWrite
    ): string {
        $sum = self::add(
            self::mul((string) $tokensInput, $rateInput),
            self::mul((string) $tokensOutput, $rateOutput),
            self::mul((string) $tokensCacheRead, $rateCacheRead),
            self::mul((string) $tokensCacheWrite, $rateCacheWrite)
        );

        return self::div($sum, self::TOKEN_UNIT);
    }

    /** Стоимость по схеме per_1m_tokens_unified: Σ tokens × rate_any / 1 000 000. */
    public static function unifiedCost(int $totalTokens, string $rateAny): string
    {
        return self::div(self::mul((string) $totalTokens, $rateAny), self::TOKEN_UNIT);
    }

    /**
     * Округление к 6 знакам после точки по правилу HALF_UP (2.5 → 3).
     * Отрицательные числа не ожидаются, но поддержка оставлена.
     */
    public static function roundHalfUp(string $value, int $places = 6): string
    {
        $value = trim(self::normalize($value));

        $negative = str_starts_with($value, '-');
        if ($negative) {
            $value = substr($value, 1);
        }
        if ($value === '') {
            $value = '0';
        }

        $dot = strpos($value, '.');
        $intPart = $dot === false ? $value : substr($value, 0, $dot);
        $fracPart = $dot === false ? '' : substr($value, $dot + 1);
        if ($intPart === '') {
            $intPart = '0';
        }

        $fracPart = str_pad($fracPart, max($places, 1) + 1, '0');
        $kept = substr($fracPart, 0, $places); // строка до $places (0..6) знаков
        $deciding = (int) substr($fracPart, $places, 1);

        $carry = $deciding >= 5 ? 1 : 0;
        $base = 10 ** $places;

        $keptInt = (int) $kept;
        $total = $keptInt + $carry;
        $newFrac = $total % $base;
        $extraInt = intdiv($total, $base);
        $intVal = (int) $intPart + $extraInt;

        $formatted = sprintf('%d.%0*s', $intVal, $places, $newFrac);
        $significant = $intVal > 0 || $newFrac > 0;

        return ($negative && $significant) ? '-'.$formatted : $formatted;
    }

    private static function normalize(string $value): string
    {
        $value = trim($value);
        if (str_starts_with($value, '+')) {
            $value = substr($value, 1);
        }

        return ($value === '' || $value === '-') ? '0' : $value;
    }

    private static function clean(string $value): string
    {
        return trim($value);
    }
}
