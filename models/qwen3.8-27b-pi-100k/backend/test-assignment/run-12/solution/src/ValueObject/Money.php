<?php

declare(strict_types=1);

namespace App\ValueObject;

use InvalidArgumentException;

/**
 * Точная десятичная величина денег.
 *
 * float **не** используется никогда — все операции bcmath, масштаб 40.
 * Округление — только {@see roundHalfUpTo6()}, правило HALF_UP до 6 знаков.
 */
final class Money
{
    private const SCALE = 40;
    private const OUTPUT_SCALE = 6;

    private string $value;

    private function __construct(string $value)
    {
        if (!preg_match('/^-?(0|[1-9]\d*)(\.\d+)?$/', $value)) {
            throw new InvalidArgumentException(sprintf('invalid Money value "%s"', $value));
        }
        $this->value = self::normalize($value);
    }

    public static function of(string $value): self
    {
        return new self(trim($value));
    }

    public static function zero(): self
    {
        return new self('0');
    }

    public function isZero(): bool
    {
        return $this->value === '0';
    }

    public function isNegative(): bool
    {
        return str_starts_with($this->value, '-');
    }

    public function add(self $other): self
    {
        return new self(bcadd($this->value, $other->value, self::SCALE));
    }

    public function subtract(self $other): self
    {
        return new self(bcsub($this->value, $other->value, self::SCALE));
    }

    public function multiply(string $factor): self
    {
        if (!preg_match('/^(0|[1-9]\d*)(\.\d+)?$/', $factor)) {
            throw new InvalidArgumentException(sprintf('invalid multiplier "%s"', $factor));
        }
        return new self(bcmul($this->value, $factor, self::SCALE));
    }

    public function negate(): self
    {
        if ($this->isZero()) {
            return self::zero();
        }
        return new self(str_starts_with($this->value, '-') ? substr($this->value, 1) : '-'.$this->value);
    }

    /** HALF_UP до 6 знаков после точки. Положительное: 2.5 → 3; отрицательное: -2.5 → -3. */
    public function roundHalfUpTo6(): self
    {
        if ($this->isZero()) {
            return self::zero();
        }

        $negative = str_starts_with($this->value, '-');
        $body = $negative ? substr($this->value, 1) : $this->value;
        [$int, $frac] = array_pad(explode('.', $body, 2), 2, '');

        // $frac[0]..$frac[5] — первые шесть знаков; $frac[6] — седьмой (решающий)
        $digit7 = $frac[self::OUTPUT_SCALE] ?? '0';
        $kept = substr($frac, 0, self::OUTPUT_SCALE);
        if (strlen($kept) < self::OUTPUT_SCALE) {
            $kept = str_pad($kept, self::OUTPUT_SCALE, '0');
        }

        if ($digit7 >= '5') {
            $result = bcadd($int.'.'.$kept, '0.000001', self::SCALE);
            $result = ($negative ? '-' : '').$result;
        } else {
            $result = ($negative ? '-' : '').$int.'.'.$kept;
        }

        return new self($result);
    }

    /** Строка ровно с 6 знаками после точки. */
    public function toFixed6(): string
    {
        if ($this->isZero()) {
            return '0.000000';
        }

        $negative = str_starts_with($this->value, '-');
        $body = $negative ? substr($this->value, 1) : $this->value;
        [$int, $frac] = array_pad(explode('.', $body, 2), 2, '');
        $frac = str_pad(substr($frac, 0, self::OUTPUT_SCALE), self::OUTPUT_SCALE, '0');
        $int = ltrim($int, '0') !== '' ? ltrim($int, '0') : '0';

        return sprintf('%s%s.%s', $negative ? '-' : '', $int, $frac);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function normalize(string $value): string
    {
        if (bccomp($value, '0', self::SCALE) === 0) {
            return '0';
        }
        // бcmath normalizes: "100" -> "100", ".5" -> ".50" — просто обрезаем ведущие нули
        $negative = str_starts_with($value, '-');
        $body = $negative ? substr($value, 1) : $value;
        $padded = bcadd($body, '0', self::SCALE); // "0.50" с 40 десятичными
        $trimmed = rtrim($padded, '0');
        if (str_ends_with($trimmed, '.')) {
            $trimmed = substr($trimmed, 0, -1);
        }
        if ($trimmed === '' || $trimmed === '.') {
            return '0';
        }
        return ($negative ? '-' : '').$trimmed;
    }
}
