<?php

declare(strict_types=1);

namespace App\Shared\Math;

use InvalidArgumentException;
use ValueError;

/**
 * Дробная арифметика для денег (bcmath, тип float не используется).
 *
 * Значения передаются и возвращаются десятичными строками.
 * Промежуточные результаты сохраняются с избыточной точностью (SCALE),
 * финальное значение события округляется ровно один раз — HALF_UP до 6 знаков
 * (см. Decimal::roundHalfUp6()).
 */
final class Decimal
{
    /** Запас точности для промежуточных вычислений (требование: ≥ 16). */
    public const SCALE = 30;

    /** Итоговая точность денег: 6 знаков после точки. */
    public const MONEY_SCALE = 6;

    private function __construct()
    {
    }

    public static function isZero(string $value): bool
    {
        return bccomp($value, '0', self::SCALE) === 0;
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
        return bcmul($a, $b, self::SCALE);
    }

    public static function div(string $a, string $b): string
    {
        if (self::isZero($b)) {
            throw new ValueError('Division by zero in Decimal::div()');
        }

        return bcdiv($a, $b, self::SCALE);
    }

    /**
     * Сумма значений (каждое уже ≤ 6 знаков после точки — итоговые
     * стоимости событий) без какого-либо округления.
     */
    public static function sum(iterable $values): string
    {
        $result = '0';
        $scale = self::MONEY_SCALE;
        foreach ($values as $value) {
            $result = bcadd($result, $value, $scale);
        }

        return self::format6($result);
    }

    /**
     * Приводит значение к строго 6 знакам после точки.
     *
     * Предупреждение: операция обрезает хвост, поэтому применять только к
     * значениям, у которых и так не больше 6 знаков (денежные суммы из
     * отчёта — сумма таких значений).
     */
    public static function format6(string $value): string
    {
        return bcdiv($value, '1', self::MONEY_SCALE);
    }

    /**
     * Округление до 6 знаков после точки, правило HALF_UP
     * (0.0000005 → 0.000001, «2.5 → 3» в масштабе задания).
     *
     * Значение должно быть неотрицательным (стоимость события).
     */
    public static function roundHalfUp6(string $value): string
    {
        if (bccomp($value, '0', self::SCALE) < 0) {
            throw new InvalidArgumentException("Value must be non-negative, got: {$value}");
        }

        // ceil при дробной части 0.5 и ниже: floor(value * 10^6 + 0.5) —
        // для неотрицательных value это ровно HALF_UP.
        $microUsd = bcadd(bcmul($value, '1000000', 64), '0.5', 0);

        return bcdiv($microUsd, '1000000', self::MONEY_SCALE);
    }

    /**
     * Канонический формат стоимости: ровно 6 знаков после десятичной точки
     * ("14.262499", "1.000000", "0.000000", и т.п.).
     *
     * Если у значения уже больше 6 знаков, сначала выполняется единственное
     * законное округление HALF_UP; иначе хвост дополняется нулями.
     */
    public static function money6(string $value): string
    {
        $v = trim($value);
        if ($v === '') {
            throw new InvalidArgumentException('Invalid decimal literal: empty value');
        }

        if (bccomp($v, '0', self::SCALE) < 0) {
            throw new InvalidArgumentException("Money values must be non-negative, got: {$v}");
        }

        $dot = strrpos($v, '.');
        $fracLen = $dot === false ? 0 : strlen($v) - $dot - 1;
        if ($fracLen > self::MONEY_SCALE) {
            return self::roundHalfUp6($v);
        }

        // fracLen ≤ 6: bcdiv масштабирует до 6 знаков без какого-либо округления.
        return bcdiv($v, '1', self::MONEY_SCALE);
    }
}
