<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Точная десятичная арифметика для денег (bcmath, масштаб >= 16).
 * float для денег запрещён — все значения передаются и возвращаются
 * decimal-строками.
 */
final class Decimals
{
    /** Рабочий масштаб промежуточных вычислений (>= 16, как требует задание). */
    public const SCALE = 20;

    /** Сколько знаков после точки в итоговой стоимости события/отчёта. */
    public const MONEY_SCALE = 6;

    /** Разрешённый формат decimal-числа из входящих данных. */
    private const DECIMAL_PATTERN = '/^\d{1,20}(?:\.\d{1,20})?$/';

    final private function __construct()
    {
    }

    /** Проверяет формат decimal-строки и возвращает её без изменений. */
    public static function assertDecimal(string $value): string
    {
        if (!preg_match(self::DECIMAL_PATTERN, $value)) {
            throw new \InvalidArgumentException(
                sprintf('Ожидается неотрицательное десятичное число, получено: "%s"', $value),
            );
        }

        return $value;
    }

    /**
     Кано́нический вид неотрицательного decimal-числа: без ведущих нулей
     и trailing-zero ("3.000000" → "3", "0.50" → "0.5").
     */
    public static function normalize(string $value): string
    {
        self::assertDecimal($value);

        $parts = explode('.', $value, 2);
        $int = ltrim($parts[0], '0') ?: '0';
        $frac = isset($parts[1]) ? rtrim($parts[1], '0') : '';

        return $frac === '' ? $int : $int.'.'.$frac;
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
        if (bccomp($b, '0', 0) <= 0) {
            throw new \InvalidArgumentException('Делитель должен быть положительным.');
        }

        return bcdiv($a, $b, self::SCALE);
    }

    private static function split(string $value): array
    {
        $parts = explode('.', $value, 2);
        if (!isset($parts[1])) {
            return [$parts[0], ''];
        }

        return [$parts[0], $parts[1]];
    }

    /**
     Единственное допустимое округление стоимости события: HALF_UP до 6 знаков.
     Стоимость события не отрицательна (токены ≥ 0, цены ≥ 0, множитель > 0),
     поэтому half-away-from-zero здесь ровно HALF_UP по определению из ТЗ.
     */
    public static function roundHalfUp6(string $value): string
    {
        self::assertDecimal($value);
        // первый знак дробной части (value * 10^6) — это и есть «половинка»
        $scaled = bcmul($value, '1000000', 12);
        [$int, $frac] = self::split($scaled);
        $micro = $int; // стоимость в «микроеддиницах» (10^-6)
        if ($frac !== '' && $frac[0] >= '5') {
            $micro = bcadd($micro, '1', 0);
        }
        // возвращаем в обычном виде ровно с 6 знаками после точки
        $micro = ltrim($micro, '0') ?: '0';
        $len = strlen($micro);
        if ($len <= 6) {
            return '0.'.str_pad($micro, 6, '0', STR_PAD_LEFT);
        }

        return substr($micro, 0, $len - 6).'.'.substr($micro, -6);
    }

    /**
     Формат стоимости/суммы ровно с 6 знаками после точки, без округления
     (вход — уже округлённая стоимость события или сумма таких значений).
     Поддерживает знак «-» для дельты repricing.
     */
    public static function format6(string $value): string
    {
        $negative = str_starts_with($value, '-');
        if ($negative) {
            $value = substr($value, 1);
        }

        self::assertDecimal($value);
        [$int, $frac] = self::split($value);
        $int = ltrim($int, '0') ?: '0';
        $frac = str_pad(substr($frac, 0, self::MONEY_SCALE), self::MONEY_SCALE, '0');
        $result = $int.'.'.$frac;

        if ($negative && bccomp($value, '0', self::SCALE) !== 0) {
            $result = '-'.$result;
        }

        return $result;
    }
}
