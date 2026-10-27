<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use InvalidArgumentException;

/**
 * Точная десятичная арифметика денег (bcmath; float запрещён).
 *
 * Значения хранятся как канонические десятичные строки: без ведущих нулей
 * (кроме '0') и без лишних хвостовых нулей после точки.
 */
final class Money
{
    /** Запас точности промежуточных вычислений (bcmath-масштаб), ≥ 16. */
    private const SCALE = 18;

    /** @param string $value десятичная строка (например "14.26249875") */
    public function __construct(private string $value)
    {
        self::assertDecimal($value);
        $this->value = self::canonical($value);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    public static function from(string $value): self
    {
        return new self($value);
    }

    public function add(self $other): self
    {
        return new self(bcadd($this->value, $other->value, self::SCALE));
    }

    public function subtract(self $other): self
    {
        return new self(bcsub($this->value, $other->value, self::SCALE));
    }

    public function multiplyBy(self $factor): self
    {
        return new self(bcmul($this->value, $factor->value, self::SCALE));
    }

    public function divideBy(self $divisor): self
    {
        if (bccomp($divisor->value, '0', self::SCALE) === 0) {
            throw new InvalidArgumentException('Division by zero.');
        }

        return new self(bcdiv($this->value, $divisor->value, self::SCALE));
    }

    /**
     * Единственное допустимое округление в цепочке расчёта: итог события
     * — до 6 знаков после точки по правилу HALF_UP (2.5 → 3, −2.5 → −3).
     */
    public function roundToMicros(): self
    {
        return new self(self::halfUp6($this->value));
    }

    public function isNegative(): bool
    {
        return str_starts_with($this->value, '-');
    }

    public function toString(): string
    {
        return $this->value;
    }

    /** Строка ровно с шестью знаками после точки (вывод в отчёты). */
    public function formatMicros(): string
    {
        $negative = str_starts_with($this->value, '-');
        $v = $negative ? substr($this->value, 1) : $this->value;
        $parts = explode('.', $v);
        $int = ltrim($parts[0], '0') ?: '0';
        $frac = substr(($parts[1] ?? '') . '000000', 0, 6);
        if ($int === '0' && $frac === '000000') {
            return '0.000000';
        }

        return ($negative ? '-' : '') . $int . '.' . $frac;
    }

    public static function compare(self $a, self $b): int
    {
        return bccomp($a->value, $b->value, self::SCALE);
    }

    /**
     * HALF_UP до 6 знаков: 2.5→3, 2.4999995→2.500000, −2.5→−3, 9.9999999→10.
     * Работает с канонической строкой; результат — каноническая строка.
     */
    public static function halfUp6(string $value): string
    {
        $negative = str_starts_with($value, '-');
        $v = $negative ? substr($value, 1) : $value;
        $parts = explode('.', $v, 2);
        $intPart = ltrim($parts[0], '0') ?: '0';
        $frac = ($parts[1] ?? '') . '000000';
        while (\strlen($frac) < 7) {
            $frac .= '0';
        }

        $intBig = (int) $intPart;
        $six = (int) substr($frac, 0, 6);

        if ($frac[6] >= '5') {
            $six += 1;
            if ($six === 1_000_000) {
                $six = 0;
                $intBig += 1;
            }
        }

        if ($six === 0) {
            return ($intBig === 0 ? '' : ($negative ? '-' : '')) . (string) $intBig;
        }

        return ($negative ? '-' : '') . $intBig . '.' . str_pad((string) $six, 6, '0', STR_PAD_LEFT);
    }

    private static function assertDecimal(string $value): void
    {
        if (!preg_match('/^-?\d+(?:\.\d+)?$/', $value)) {
            throw new InvalidArgumentException(sprintf('Invalid decimal value: "%s".', $value));
        }
    }

    /** Каноническая форма: без ведущих нулей и лишних хвостовых. */
    private static function canonical(string $value): string
    {
        $negative = str_starts_with($value, '-');
        $v = ltrim($value, '-');
        $parts = explode('.', $v, 2);
        $int = ltrim($parts[0], '0') ?: '0';
        $frac = rtrim($parts[1] ?? '', '0');
        if ($frac !== '') {
            return ($negative ? '-' : '') . $int . '.' . $frac;
        }

        return ($negative && $int !== '0' ? '-' : '') . $int;
    }
}
