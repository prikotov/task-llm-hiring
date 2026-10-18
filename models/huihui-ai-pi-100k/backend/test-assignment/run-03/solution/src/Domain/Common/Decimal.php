<?php

declare(strict_types=1);

namespace App\Domain\Common;

use InvalidArgumentException;

/**
 * Неделимое десятичное число на точной bcmath-арифметике (без float).
 *
 * Промежуточные вычисления ведутся с запасом точности SCALE;
 * денежные величины приводятся к MONEY_SCALE знакам после десятичной точки.
 */
final class Decimal
{
    /** Запас точности для промежуточных вычислений. */
    public const SCALE = 16;

    /** Число знаков после точки в итоговых деньгах (cost_usd, дельты). */
    public const MONEY_SCALE = 6;

    private function __construct(private readonly string $value)
    {
    }

    public static function of(string|int|float $value): self
    {
        if (is_float($value) && !is_finite($value)) {
            throw new InvalidArgumentException('Дробное значение должно быть конечным числом.');
        }

        $value = trim((string) $value);
        if (!preg_match('/^[+-]?\d+(\.\d+)?$/', $value)) {
            throw new InvalidArgumentException(sprintf('Некорректный десятичный литерал "%s".', $value));
        }
        if ($value[0] === '+') {
            $value = substr($value, 1);
        }

        return new self($value);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    /** Десятичная строка без ведущего «+». */
    public function value(): string
    {
        return $this->value;
    }

    public function isNegative(): bool
    {
        return str_starts_with($this->value, '-');
    }

    public function isZero(): bool
    {
        return bccomp($this->value, '0', self::SCALE) === 0;
    }

    public function add(self $other): self
    {
        return new self(bcadd($this->value, $other->value, self::SCALE));
    }

    public function sub(self $other): self
    {
        return new self(bcsub($this->value, $other->value, self::SCALE));
    }

    public function mul(self $other): self
    {
        return new self(bcmul($this->value, $other->value, self::SCALE));
    }

    public function div(self $other): self
    {
        if ($other->isZero()) {
            throw new InvalidArgumentException('Деление на ноль.');
        }

        return new self(bcdiv($this->value, $other->value, self::SCALE));
    }

    public function equals(self $other): bool
    {
        return bccomp($this->value, $other->value, self::SCALE) === 0;
    }

    /**
     * Округление до $scale знаков после точки, правило HALF_UP
     * (2.5 → 3 у нуля; для отрицательных — полуокругление от нуля).
     */
    public function roundHalfUp(int $scale): self
    {
        if ($scale < 0) {
            throw new InvalidArgumentException('Порядок округления не может быть отрицательным.');
        }

        $factor = bcpow('10', (string) $scale, 0);
        $negative = $this->isNegative();
        $absolute = $negative ? substr($this->value, 1) : $this->value;

        // floor(|x| * 10^scale + 0.5): bcadd с null-порядком отбрасывает дробную хвостовую часть у неотрицательных.
        $scaled = bcadd(bcmul($absolute, $factor, self::SCALE), '0.5', 0);
        $rounded = bcdiv($scaled, $factor, $scale);

        if ($negative && bccomp($rounded, '0', $scale) !== 0) {
            $rounded = '-'.$rounded;
        }

        return new self($rounded);
    }

    /** Строка в точности с $scale знаками после точки (например, '14.262499'). */
    public function format(int $scale): string
    {
        if ($scale < 0) {
            throw new InvalidArgumentException('Число знаков не может быть отрицательным.');
        }
        if ($this->isZero()) {
            return '0'.($scale > 0 ? '.'.str_repeat('0', $scale) : '');
        }

        return bcdiv($this->value, '1', $scale);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
