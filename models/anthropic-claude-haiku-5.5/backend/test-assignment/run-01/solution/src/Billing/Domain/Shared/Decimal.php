<?php

declare(strict_types=1);

namespace App\Billing\Domain\Shared;

/**
 * Денежная арифметика только через bcmath. Промежуточные значения — со
 * шкалой SCALE; единственное округление — roundEvent() (HALF_UP до 6 знаков).
 */
final class Decimal
{
    public const SCALE = 30;
    public const MONEY_SCALE = 6;

    private const HALF_UNIT = '0.0000005';

    public static function add(string $a, string $b, int $scale = self::SCALE): string
    {
        return bcadd($a, $b, $scale);
    }

    public static function mul(string $a, string $b, int $scale = self::SCALE): string
    {
        return bcmul($a, $b, $scale);
    }

    public static function div(string $a, string $b, int $scale = self::SCALE): string
    {
        return bcdiv($a, $b, $scale);
    }

    public static function compare(string $a, string $b, int $scale = self::SCALE): int
    {
        return bccomp($a, $b, $scale);
    }

    /**
     * Округление стоимости события: HALF_UP до 6 знаков.
     * bcadd усекает результат, поэтому добавляем половину единицы последнего знака
     * (с учётом знака для отрицательных значений).
     */
    public static function roundEvent(string $value): string
    {
        $isNegative = bccomp($value, '0', self::SCALE) < 0;

        return $isNegative
            ? bcsub($value, self::HALF_UNIT, self::MONEY_SCALE)
            : bcadd($value, self::HALF_UNIT, self::MONEY_SCALE);
    }

    public static function formatMoney(string $value): string
    {
        return bcadd($value, '0', self::MONEY_SCALE);
    }
}
