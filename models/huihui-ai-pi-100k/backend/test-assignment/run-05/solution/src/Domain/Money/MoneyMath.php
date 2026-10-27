<?php

declare(strict_types=1);

namespace App\Domain\Money;

use function bcadd;
use function bccomp;
use function bcdiv;
use function bcsub;
use function bcmul;
use function explode;
use function str_contains;
use function str_pad;
use function str_starts_with;
use function substr;
use function trim;

/**
 * Десятичная арифметика для денег на базе bcmath.
 *
 * Правила (см. TASK.md, п. 4):
 *  - промежуточные вычисления — без округления (масштаб 24 — с запасом
 *    точности; точная стоимость события занимает не более 18 знаков);
 *  - единственное округление — HALF_UP до 6 знаков после точки,
 *    применяется к итоговой стоимости события;
 *  - float для денег не используется.
 */
final class MoneyMath
{
    /** Запас точности промежуточных вычислений. */
    public const SCALE = 24;

    /** Число знаков после точки в итоговом денежном значении. */
    public const AMOUNT_SCALE = 6;

    /** Делитель цены «за 1 000 000 токенов». */
    public const TOKENS_PER_UNIT = '1000000';

    public static function add(string ...$values): string
    {
        $sum = '0';
        foreach ($values as $value) {
            $sum = bcadd($sum, $value, self::SCALE);
        }

        return $sum;
    }

    public static function sum(array $values): string
    {
        return self::add(...$values);
    }

    public static function sub(string $a, string $b): string
    {
        return bcsub(self::clean($a), self::clean($b), self::SCALE);
    }

    public static function mul(string $a, string $b): string
    {
        return bcmul(self::clean($a), self::clean($b), self::SCALE);
    }

    public static function div(string $a, string $b): string
    {
        return bcdiv(self::clean($a), self::clean($b), self::SCALE);
    }

    /**
     * Округление HALF_UP до $scale знаков после точки (2.5 → 3;
     * для отрицательных чисел — от нуля).
     *
     * @return string десятичная строка ровно с $scale знаками после точки
     */
    public static function round(string $value, int $scale = self::AMOUNT_SCALE): string
    {
        $value = trim($value);
        if ($value === '' || $value === '-' || $value === '+') {
            throw new \InvalidArgumentException('Пустое десятичное значение');
        }

        $negative = str_starts_with($value, '-');
        if ($negative || str_starts_with($value, '+')) {
            $value = substr($value, 1);
        }

        if (!str_contains($value, '.')) {
            $value .= '.';
        }

        [$int, $frac] = explode('.', $value, 2);
        $int = $int === '' ? '0' : $int;
        $frac = str_pad($frac, $scale + 1, '0');

        $keep = substr($frac, 0, $scale);
        $nextDigit = $frac[$scale];

        $base = $keep === '' ? $int : $int . '.' . $keep;
        $increment = $scale === 0 ? '1' : '0.' . str_repeat('0', $scale - 1) . '1';

        $result = $nextDigit >= '5'
            ? bcadd($base, $increment, $scale)
            : bcadd($base, '0', $scale);

        if ($negative && bccomp($result, '0', $scale) > 0) {
            $result = '-' . $result;
        }

        return $result;
    }

    /**
     * Нормализация значения, не требующего округления (например, суммы
     * величин, у которых уже не более 6 знаков), к формату «ровно 6 знаков»
     * без изменения числового значения.
     */
    public static function normalize6(string $value): string
    {
        return bcadd(trim($value), '0', self::AMOUNT_SCALE);
    }

    private static function clean(string $value): string
    {
        $value = trim($value);

        if ($value === '' || $value === '-' || $value === '+') {
            throw new \InvalidArgumentException('Пустое десятичное значение');
        }

        return $value;
    }
}
