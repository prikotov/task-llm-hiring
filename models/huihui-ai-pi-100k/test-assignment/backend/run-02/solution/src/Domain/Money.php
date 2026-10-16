<?php

declare(strict_types=1);

namespace App\Domain;

use ValueError;

use function bccomp;
use function bcdiv;
use function bcsub;
use function bcadd;
use function bcmul;

/**
 * Сумма денег с точной десятичной арифметикой (bcmath).
 *
 * Тип float для денег не используется: внутреннее представление —
 * каноническая десятичная строка. Все промежуточные вычисления идут
 * с масштабом SCALE (>= 16 разрядов); округление — явное, правило HALF_UP.
 */
final class Money
{
    /** Масштаб (разрядность) промежуточных вычислений. */
    public const SCALE = 16;

    /** Стабильная точность стоимости событий и отчётов (6 знаков). */
    public const US_DECIMALS = 6;

    private readonly string $amount;

    private function __construct(
        string $amount,
    ) {
        $this->amount = self::canonicalize($amount);
    }

    /**
     * @throws ValueError при некорректной десятичной строке
     */
    public static function fromDecimalString(string $decimal): self
    {
        self::assertDecimal($decimal);

        return new self($decimal);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    public function plus(self $other): self
    {
        return new self(bcadd($this->amount, $other->amount, self::SCALE));
    }

    public function minus(self $other): self
    {
        return new self(bcsub($this->amount, $other->amount, self::SCALE));
    }

    /**
     * @param string $factor десятичная строка, например «0.5»
     */
    public function times(string $factor): self
    {
        self::assertDecimal($factor);

        return new self(bcmul($this->amount, $factor, self::SCALE));
    }

    /**
     * @param string $denominator десятичная строка (например «1000000»)
     */
    public function dividedBy(string $denominator): self
    {
        self::assertDecimal($denominator);

        return new self(bcdiv($this->amount, $denominator, self::SCALE));
    }

    /**
     * Округление до $decimals знаков после точки по правилу HALF_UP
     * (2.5 → 3). Корректно для неотрицательных сумм, которые использует тарификация.
     */
    public function roundHalfUp(int $decimals): self
    {
        if ($decimals < 0 || $decimals > 12) {
            throw new ValueError('Money::roundHalfUp: число знаков должно быть в диапазоне 0..12');
        }

        $factor = '1' . str_repeat('0', $decimals);
        $shifted = bcmul($this->amount, $factor, self::SCALE);
        $integer = bcdiv($shifted, '1', 0); // целая часть (отбрасывание хвоста)
        $fraction = bcsub($shifted, $integer, 1); // дробный остаток в [0; 1)

        if (bccomp($fraction, '0.5', 1) >= 0) {
            $integer = bcadd($integer, '1', 0);
        }

        return new self(bcdiv($integer, $factor, $decimals));
    }

    /** Каноническая десятичная строка (без ведущих нулей и «хвоста» после точки). */
    public function toDecimalString(): string
    {
        return $this->amount;
    }

    /**
     * Форматирование ровно в $decimals знаков после точки (округление HALF_UP).
     */
    public function format(int $decimals = self::US_DECIMALS): string
    {
        $value = $this->roundHalfUp($decimals)->toDecimalString();

        $negative = str_starts_with($value, '-');
        if ($negative) {
            $value = substr($value, 1);
        }

        $parts = explode('.', $value, 2);
        $intPart = ltrim($parts[0], '0') ?: '0';
        $fracPart = str_pad(substr($parts[1] ?? '', 0, $decimals), $decimals, '0');

        $isZero = $intPart === '0' && $fracPart === str_repeat('0', $decimals);

        return ($negative && !$isZero ? '-' : '') . $intPart . '.' . $fracPart;
    }

    private static function assertDecimal(string $value): void
    {
        if (preg_match('/^[+-]?\d+(\.\d+)?$/', $value) !== 1) {
            throw new ValueError('Money: недопустимая десятичная строка: ' . $value);
        }
    }

    private static function canonicalize(string $decimal): string
    {
        $negative = str_starts_with($decimal, '-');
        $value = ltrim($decimal, '+-');

        $parts = explode('.', $value, 2);
        $intPart = ltrim($parts[0], '0') ?: '0';
        $fracPart = rtrim($parts[1] ?? '', '0');

        if ($fracPart !== '') {
            return ($negative ? '-' : '') . $intPart . '.' . $fracPart;
        }

        return ($negative && $intPart !== '0' ? '-' : '') . $intPart;
    }
}
