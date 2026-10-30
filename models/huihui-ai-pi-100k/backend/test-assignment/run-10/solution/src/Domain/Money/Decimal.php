<?php

declare(strict_types=1);

namespace App\Domain\Money;

use RuntimeException;

/**
 * Небольшая обёртка над bcmath для работы с деньгами.
 *
 * Везде используется десятичная арифметика с запасом точности
 * (SCALE >= 16, требование задания); тип float для денег запрещён.
 */
final class Decimal
{
    /**
     * Запас точности промежуточных вычислений (требование задания: >= 16).
     */
    public const SCALE = 20;

    private const MILLION = '1000000';

    private const DECIMAL_RE = '/^(-?)([0-9]+)(?:\.([0-9]+))?$/';

    private function __construct()
    {
    }

    public static function mul(string|int $a, string|int $b): string
    {
        return bcmul((string) $a, (string) $b, self::SCALE);
    }

    public static function add(string|int $a, string|int $b): string
    {
        return bcadd((string) $a, (string) $b, self::SCALE);
    }

    public static function sub(string|int $a, string|int $b): string
    {
        return bcsub((string) $a, (string) $b, self::SCALE);
    }

    public static function div(string|int $a, string|int $b): string
    {
        return bcdiv((string) $a, (string) $b, self::SCALE);
    }

    /**
     * Деление на 1 000 000 (нормализация «цена за 1M токенов»).
     */
    public static function perMillion(string|int $value): string
    {
        return bcdiv((string) $value, self::MILLION, self::SCALE);
    }

    /**
     * Округление до $places знаков после точки, правило HALF_UP
     * (0 и более в отброшённом хвосте решается по первому отброшенному
     * знаку: >= 5 => вверх).
     *
     * Результат всегда содержит ровно $places знаков после точки.
     */
    public static function roundHalfUp(string $value, int $places = 6): string
    {
        if ($places < 0) {
            throw new \InvalidArgumentException('places must be >= 0');
        }

        [$sign, $int, $frac] = self::decompose($value);
        $frac = str_pad($frac, $places, '0');           // удлинение без урезания
        $keep = substr($frac, 0, $places);               // значимые цифры
        $rest = substr($frac, $places);                  // отбрасываемый хвост

        $scale = $int . $keep;                             // число * 10^places, ещё с плавом
        if ($rest !== '' && $rest[0] >= '5') {             // HALF_UP
            $scale = bcadd($scale, '1', 0);
        }

        return self::assemble($sign, $scale, $places);
    }

    /**
     * Нормализует представление числа к ровно 6 знакам после точки,
     * не меняя его значения. Используется для сумм отчёта (сумма чисел
     * с <= 6 знаками имеет <= 6 знаков, поэтому значение сохраняется).
     */
    public static function toSixDecimals(string $value): string
    {
        [$sign, $int, $frac] = self::decompose($value);
        $frac = substr(str_pad($frac, 6, '0'), 0, 6);

        return $sign . $int . '.' . $frac;
    }

    /**
     * Проверяет, что $value — корректное десятичное число
     * (опциональный знак, целые, опциональная дробная), возвращает
     * (sign, intPart, fracPart). Дробная часть не нормализуется
     * в длину.
     */
    private static function decompose(string $value): array
    {
        $value = trim($value);
        if (preg_match(self::DECIMAL_RE, $value, $m) !== 1) {
            throw new RuntimeException('Not a decimal number: "' . $value . '"');
        }

        return [$m[1] === '' ? '' : '-', $m[2], $m[3] ?? ''];
    }

    private static function assemble(string $sign, string $scale, int $places): string
    {
        $scale = ltrim($scale, '0');
        $scale = $scale === '' ? '0' : $scale;

        if ($places === 0) {
            return $sign . $scale;
        }

        if (strlen($scale) <= $places) {
            return $sign . '0.' . str_pad($scale, $places, '0', STR_PAD_LEFT);
        }

        return $sign . substr($scale, 0, -$places) . '.' . substr($scale, -$places);
    }
}
