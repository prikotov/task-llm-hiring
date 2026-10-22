<?php

declare(strict_types=1);

namespace App\Domain\Money;

use InvalidArgumentException;

use function bccomp;
use function bcadd;
use function bcdiv;
use function bcmul;
use function array_pad;
use function explode;
use function ltrim;
use function preg_match;
use function rtrim;
use function str_contains;
use function str_repeat;
use function str_starts_with;
use function strlen;
use function substr;
use function trim;

/**
 * Immutable arbitrary-precision decimal number.
 *
 * All money and rate arithmetic in the application goes through this class:
 * floats are never used. The internal calculation scale is deliberately much
 * larger than the six decimal places required by the pricing specification.
 */
final readonly class Decimal
{
    public const int CALC_SCALE = 30;

    /**
     * @param numeric-string $value
     */
    private function __construct(private string $value)
    {
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);

        if ($value === '' || preg_match('/^[+-]?(?:\d+(?:\.\d*)?|\.\d+)$/', $value) !== 1) {
            throw new InvalidArgumentException(sprintf('Invalid decimal value "%s".', $value));
        }

        return new self(self::normalize($value));
    }

    public static function fromInt(int $value): self
    {
        return new self((string) $value);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    public function add(self $other): self
    {
        return new self(self::normalize(bcadd($this->value, $other->value, self::CALC_SCALE)));
    }

    public function subtract(self $other): self
    {
        return new self(self::normalize(bcadd($this->value, '-' . $other->value, self::CALC_SCALE)));
    }

    public function multiply(self $other): self
    {
        return new self(self::normalize(bcmul($this->value, $other->value, self::CALC_SCALE)));
    }

    public function divide(self $other): self
    {
        if ($other->isZero()) {
            throw new InvalidArgumentException('Division by zero.');
        }

        return new self(self::normalize(bcdiv($this->value, $other->value, self::CALC_SCALE)));
    }

    public function compareTo(self $other): int
    {
        return bccomp($this->value, $other->value, self::CALC_SCALE);
    }

    public function equals(self $other): bool
    {
        return $this->compareTo($other) === 0;
    }

    public function isZero(): bool
    {
        return $this->compareTo(self::zero()) === 0;
    }

    public function isNegative(): bool
    {
        return $this->compareTo(self::zero()) < 0;
    }

    public function isNegativeOrZero(): bool
    {
        return $this->compareTo(self::zero()) <= 0;
    }

    /**
     * Rounds away from zero when the first discarded digit is five or more.
     */
    public function roundHalfUp(int $scale): self
    {
        if ($scale < 0) {
            throw new InvalidArgumentException('Scale must be non-negative.');
        }

        $negative = $this->isNegative();
        $absolute = $negative ? substr($this->value, 1) : $this->value;
        [$integer, $fraction] = array_pad(explode('.', $absolute, 2), 2, '');

        if (strlen($fraction) <= $scale) {
            return $negative ? new self(self::normalize('-' . $absolute)) : $this;
        }

        $keptFraction = substr($fraction, 0, $scale);
        $firstDiscarded = $fraction[$scale];
        $base = $scale > 0 ? $integer . '.' . $keptFraction : $integer;

        if ($firstDiscarded >= '5') {
            $increment = $scale > 0
                ? '0.' . str_repeat('0', $scale - 1) . '1'
                : '1';
            $base = bcadd($base, $increment, $scale);
        }

        $rounded = self::normalize(($negative ? '-' : '') . $base);

        return new self($rounded);
    }

    /**
     * Fixed-point representation with exactly $scale digits after the point.
     * Used only when writing money to the database or the report.
     */
    public function format(int $scale): string
    {
        $rounded = $this->roundHalfUp($scale);

        return bcadd($rounded->value, '0', $scale);
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * @return numeric-string
     */
    private static function normalize(string $value): string
    {
        $value = trim($value);

        if (str_starts_with($value, '+')) {
            $value = substr($value, 1);
        }

        $negative = str_starts_with($value, '-');
        if ($negative) {
            $value = substr($value, 1);
        }

        if (!str_contains($value, '.')) {
            $value .= '.';
        }

        [$integer, $fraction] = array_pad(explode('.', $value, 2), 2, '');

        $integer = ltrim($integer, '0');
        if ($integer === '') {
            $integer = '0';
        }

        $fraction = rtrim($fraction, '0');

        $normalized = $integer . ($fraction === '' ? '' : '.' . $fraction);
        if ($negative && !self::isZeroString($normalized)) {
            $normalized = '-' . $normalized;
        }

        return $normalized;
    }

    private static function isZeroString(string $value): bool
    {
        return (bool) preg_match('/^0(?:\.0*)?$/', $value);
    }
}
