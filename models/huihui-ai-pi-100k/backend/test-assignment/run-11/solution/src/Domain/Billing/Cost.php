<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use InvalidArgumentException;

/**
 * Иммутабельная денежная величина на точной десятичной арифметике (bcmath).
 *
 * float для денег запрещён (TASK.md п. 4). Промежуточные вычисления ведутся
 * с масштабом 20 (резерв точности ≥ 16). Единственное округление событий —
 * HALF_UP до 6 знаков, см. {@see Cost::roundHalfUp6()}.
 *
 * @readonly
 */
final class Cost
{
    private const SCALE_INTERNAL = 20;

    private string $decimal;

    /**
     * @param string $decimal десятичное число, напр. "14.26249875"
     */
    public static function make(string $decimal): self
    {
        return new self($decimal);
    }

    private function __construct(string $decimal)
    {
        if (!preg_match('/^[+-]?\d+(\.\d+)?$/', $decimal)) {
            throw new InvalidArgumentException("Invalid decimal literal: \"{$decimal}\"");
        }
        $this->decimal = self::canonicalize($decimal);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    public static function sum(array $costs): self
    {
        $total = '0';
        foreach ($costs as $cost) {
            $total = bcadd($total, $cost->decimal, self::SCALE_INTERNAL);
        }

        return new self($total);
    }

    public function add(self $other): self
    {
        return new self(bcadd($this->decimal, $other->decimal, self::SCALE_INTERNAL));
    }

    public function subtract(self $other): self
    {
        return new self(bcsub($this->decimal, $other->decimal, self::SCALE_INTERNAL));
    }

    public function multiply(self $other): self
    {
        return new self(bcmul($this->decimal, $other->decimal, self::SCALE_INTERNAL));
    }

    public function isNegative(): bool
    {
        return bccomp($this->decimal, '0', self::SCALE_INTERNAL) < 0;
    }

    public function equalsZero(): bool
    {
        return bccomp($this->decimal, '0', self::SCALE_INTERNAL) === 0;
    }

    public function decimal(): string
    {
        return $this->decimal;
    }

    /**
     * Десятичная запись с ровно 6 знаками после точки (обрезка/досчёт нулями,
     * НЕ округление).
     */
    public function toSixDecimalsTruncated(): string
    {
        [$int, $frac] = self::split($this->decimal);

        return "{$int}." . self::padFrac6($frac);
    }

    /**
     * HALF_UP-округление до 6 знаков после точки (без float).
     * Для отрицательных величин — «от нуля» (TASK.md п. 4: 2.5 → 3).
     */
    public static function roundHalfUp6(self $value): self
    {
        $negative = str_starts_with($value->decimal, '-');
        $abs = ltrim($value->decimal, '-');

        // Число в юнитах 1e-6: |x| * 1e6.
        $scaled = bcadd(bcmul($abs, '1000000', self::SCALE_INTERNAL), '0', 0);
        $units = (int) $scaled;
        // Дробная часть |x|*1e6:
        $frac = bcsub(bcmul($abs, '1000000', self::SCALE_INTERNAL), $scaled, self::SCALE_INTERNAL);
        if (bccomp($frac, '0.5', self::SCALE_INTERNAL) >= 0) {
            $units += 1;
        }

        $int = (string) intdiv($units, 1000000);
        $frac6 = str_pad((string) ($units % 1000000), 6, '0', STR_PAD_LEFT);
        $sign = $negative && $units > 0 ? '-' : '';

        return new self("{$sign}{$int}.{$frac6}");
    }

    /**
     * @return array{0:string, 1:string}
     */
    private static function split(string $dec): array
    {
        $abs = ltrim($dec, '-');
        $parts = preg_split('/\./', $abs, 2) ?: ['0', ''];
        $int = ltrim($parts[0], '0') ?: '0';

        return [$int, $parts[1] ?? ''];
    }

    private static function padFrac6(?string $frac): string
    {
        $frac = (string) $frac;

        return strlen($frac) < 6 ? str_pad($frac, 6, '0') : substr($frac, 0, 6);
    }

    private static function canonicalize(string $dec): string
    {
        $negative = str_starts_with($dec, '-');
        $abs = ltrim($dec, '-');
        $parts = preg_split('/\./', $abs, 2) ?: ['0', ''];
        $int = ltrim($parts[0], '0') ?: '0';
        $frac = $parts[1] ?? '';

        if ($frac !== '') {
            $frac = substr(str_pad($frac, self::SCALE_INTERNAL, '0'), 0, self::SCALE_INTERNAL);
            $trimmed = rtrim($frac, '0');
            if ($trimmed !== '') {
                return ($negative && $int !== '0' ? '-' : '') . "{$int}.{$trimmed}";
            }
        }

        return $negative && $int !== '0' ? "-{$int}" : $int;
    }
}
