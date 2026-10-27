<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

/**
 * Decimal money amount with arbitrary precision (bcmath, scale 20).
 * Floats are never used.
 */
final class Money
{
    private const SCALE = 20;

    private function __construct(private readonly string $amount)
    {
        if (preg_match('/^-?\d+(\.\d+)?$/', $amount) !== 1) {
            throw new \InvalidArgumentException("Invalid decimal amount: {$amount}");
        }
    }

    public static function from(string $decimal): self
    {
        return new self(ltrim($decimal, '+'));
    }

    public static function zero(): self
    {
        return new self('0');
    }

    public function add(self $other): self
    {
        return new self(bcadd($this->amount, $other->amount, self::SCALE));
    }

    public function subtract(self $other): self
    {
        return new self(bcsub($this->amount, $other->amount, self::SCALE));
    }

    /** Multiply by a decimal factor, no rounding. */
    public function multiplyBy(string $factor): self
    {
        return new self(bcmul($this->amount, $factor, self::SCALE));
    }

    /**
     * Round to exactly $places after the dot, HALF_UP (2.5 -> 3).
     */
    public function roundTo(int $places = 6): Money
    {
        return new self(self::halfUp($this->amount, $places));
    }

    /** Format with exactly $places decimals (value already appropriate). */
    public function format(int $places = 6): string
    {
        return self::pad($this->amount, $places);
    }

    public function toString(): string
    {
        return $this->amount;
    }

    private static function isZero(string $n): bool
    {
        return bccomp($n, '0', self::SCALE) === 0;
    }

    /** Decimal HALF_UP: 2.5 -> 3, 2.4999995 (6dp) -> 2.500000. */
    private static function halfUp(string $n, int $places): string
    {
        if ($places < 0) {
            throw new \InvalidArgumentException('Places must be >= 0');
        }
        $negative = str_starts_with($n, '-');
        $abs = ltrim($n, '-');
        [$int, $frac] = array_pad(explode('.', $abs, 2), 2, '');
        $frac = str_pad($frac, $places + 1, '0');
        $keep = substr($frac, 0, $places);
        $digit = (int) ($frac[$places] ?? 0);
        if ($digit >= 5) {
            [$int, $keep] = self::incrementDecimal($int, $keep);
        }
        $result = $int . ($places > 0 ? '.' . $keep : '');
        if ($negative && self::isZero($result)) {
            return $places > 0 ? '0.' . str_repeat('0', $places) : '0';
        }
        return $negative ? '-' . $result : $result;
    }

    private static function incrementDecimal(string $int, string $frac): array
    {
        if (self::isZero($frac)) {
            return [bcadd($int, '1', 0), ''];
        }
        $len = strlen($frac);
        $digits = str_split($frac);
        $carry = 1;
        for ($i = $len - 1; $i >= 0 && $carry; $i--) {
            $v = (int) $digits[$i] + $carry;
            $carry = intdiv($v, 10);
            $digits[$i] = (string) ($v % 10);
        }
        if ($carry !== 0) {
            $int = bcadd($int, (string) $carry, 0);
        }
        return [(string) $int, implode('', $digits)];
    }

    /** Pad/trim to exactly $places decimals. */
    private static function pad(string $n, int $places): string
    {
        if ($places < 0) {
            throw new \InvalidArgumentException('Places must be >= 0');
        }
        if (self::isZero($n)) {
            return $places > 0 ? '0.' . str_repeat('0', $places) : '0';
        }
        $negative = str_starts_with($n, '-');
        $abs = ltrim($n, '-');
        [$int, $frac] = array_pad(explode('.', $abs, 2), 2, '');
        // Trim trailing zeros for stable length handling.
        if ($frac !== '') {
            $frac = rtrim($frac, '0');
        }
        if (strlen($frac) > $places) {
            $frac = substr($frac, 0, $places);
        }
        $frac = str_pad($frac, $places, '0');
        if (self::isZero($int) && self::isZero($frac)) {
            return $places > 0 ? '0.' . str_repeat('0', $places) : '0';
        }
        $result = $int . ($places > 0 ? '.' . $frac : '');
        return $negative ? '-' . $result : $result;
    }
}
