<?php

declare(strict_types=1);

namespace App\Domain\Money;

use InvalidArgumentException;

/**
 * Денежная сумма в точной десятичной арифметике (bcmath, без float).
 *
 * Внутреннее значение хранится как десятичная строка с запасом точности
 * (масштаб 20 знаков) без промежуточного округления; единственным округлением
 * является {@see roundedHalfUpToFixed()} — до 6 знаков после точки HALF_UP.
 */
final class Money
{
    /** Запас точности для промежуточных вычислений (>= 16 по требованию задания). */
    public const DECIMAL_SCALE = 20;

    /** Финальный масштаб: 6 знаков после точки. */
    public const FIXED_SCALE = 6;

    private const UNITS_PER_FIXED_DIGIT = '1000000';

    private function __construct(
        private readonly string $value,
    ) {
    }

    public static function zero(): self
    {
        return new self('0');
    }

    /**
     * Сумма из точной десятичной строки (float-представления в ходу быть не должно).
     */
    public static function exact(string $value): self
    {
        self::assertDecimal($value);

        return new self(bcadd($value, '0', self::DECIMAL_SCALE));
    }

    /**
     * Сумма, уже имеющая не более 6 знаков после точки (стоимость рассчитанного события).
     */
    public static function fromFixed6(string $value): self
    {
        self::assertDecimal($value);

        return self::exact($value);
    }

    public function add(self $other): self
    {
        return new self(bcadd($this->value, $other->value, self::DECIMAL_SCALE));
    }

    public function subtract(self $other): self
    {
        return new self(bcsub($this->value, $other->value, self::DECIMAL_SCALE));
    }

    /**
     * Умножение на десятичный множитель (rate, off-peak multiplier и т.п.).
     */
    public function multipliedBy(string $factor): self
    {
        self::assertDecimal($factor);

        return new self(bcmul($this->value, $factor, self::DECIMAL_SCALE));
    }

    /**
     * Деление на целый положительные делитель (например, 1 000 000 токенов).
     */
    public function dividedBy(int|string $divisor): self
    {
        $divisor = (string) $divisor;
        if (ctype_digit($divisor) === false || bccomp($divisor, '0', 0) === 0) {
            throw new InvalidArgumentException(
                'Divisor must be a positive integer, got: '.$divisor,
            );
        }

        return new self(bcdiv($this->value, $divisor, self::DECIMAL_SCALE));
    }

    /**
     * Единственное разрешённое округление: до 6 знаков после точки, HALF_UP
     * (равная половина — в сторону, отдалённую от нуля; 2.5 → 3, -2.5 → -3).
     * Для неотрицательных значений — floor(x + 1/2); для отрицательных
     * симметрично: -floor(-x + 1/2), что в bcmath сводится к
     * truncate(±x ∓ 1/2).
     */
    public function roundedHalfUpToFixed(): self
    {
        $scaled = bcmul($this->value, self::UNITS_PER_FIXED_DIGIT, self::DECIMAL_SCALE);

        if (bccomp($scaled, '0', 0) >= 0) {
            $intPart = bcdiv(bcadd($scaled, '0.5', self::DECIMAL_SCALE), '1', 0);
        } else {
            $intPart = bcdiv(bcsub($scaled, '0.5', self::DECIMAL_SCALE), '1', 0);
        }

        return new self(bcdiv($intPart, self::UNITS_PER_FIXED_DIGIT, self::FIXED_SCALE));
    }

    /**
     * Десятичная строка ровно с 6 знаками после точки (трuncation допустима
     * только для значений, не превышающих 6 знаков — сумма зафиксированных стоимостей).
     */
    public function asFixed6String(): string
    {
        return bcdiv($this->value, '1', self::FIXED_SCALE);
    }

    public function absoluteValue(): self
    {
        return bccomp($this->value, '0', 0) < 0
            ? new self(bcsub('0', $this->value, self::DECIMAL_SCALE))
            : $this;
    }

    public function isNegative(): bool
    {
        return bccomp($this->value, '0', 0) < 0;
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return bccomp($this->value, $other->value, self::DECIMAL_SCALE) === 0;
    }

    private static function assertDecimal(string $value): void
    {
        if (preg_match('/^-?\d+(\.\d+)?$/', $value) !== 1) {
            throw new InvalidArgumentException(sprintf('Invalid decimal value "%s".', $value));
        }
    }
}
