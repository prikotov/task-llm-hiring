<?php

declare(strict_types=1);

namespace App\Domain\Money;

use InvalidArgumentException;

/**
 * Иммутабельная стоимость в USD.
 *
 * Деньги — десятичная строка (bcmath), float запрещён.
 * Промежуточные вычисления — с масштабом COST_SCALE (>= 16),
 * окончательное округление стоимости события — 6 знаков, HALF_UP.
 */
final readonly class Cost
{
    public const COST_SCALE = 16;

    private function __construct(
        private string $value,
    ) {
        if (!preg_match('/^-?\d{1,14}(\.\d{1,16})?$/', $this->value)) {
            throw new InvalidArgumentException(
                sprintf('Некорректное десятичное значение стоимости: %s', $this->value)
            );
        }
    }

    /** @param string $value десятичная строка, например "14.26249875" */
    public static function fromString(string $value): self
    {
        $value = trim($value);

        if (!preg_match('/^-?\d+(\.\d+)?$/', $value)) {
            throw new InvalidArgumentException(
                sprintf('Некорректное десятичное значение стоимости: %s', $value)
            );
        }

        $normalized = rtrim(rtrim((string) bcmul($value, '1', self::COST_SCALE), '0'), '.');

        return new self($normalized === '' ? '0' : $normalized);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    /**
     * cost = tokens * rate / 1_000_000, точность до масштаба COST_SCALE, без округления.
     *
     * @param int    $tokens целое >= 0
     * @param string $rate   десятичная строка, 6 знаков после точки
     */
    public static function perTokens(int $tokens, string $rate): self
    {
        $num = bcmul((string) $tokens, $rate, self::COST_SCALE + 6);
        $value = bcdiv($num, '1000000', self::COST_SCALE);

        return self::fromString($value);
    }

    public static function sum(Cost ...$costs): self
    {
        $acc = '0';
        foreach ($costs as $cost) {
            $acc = bcadd($acc, $cost->value, self::COST_SCALE);
        }

        return self::fromString($acc);
    }

    /** @param string $multiplier десятичная строка, например "0.5" */
    public function multiplyBy(string $multiplier): self
    {
        return self::fromString(bcmul($this->value, $multiplier, self::COST_SCALE));
    }

    /**
     * Округление ровно одно: итоговая стоимость события — 6 знаков после точки, HALF_UP.
     */
    public function roundForEvent(): self
    {
        return self::fromString($this->roundTo(6));
    }

    /** $this - $other (математически может быть отрицательной), без округления. */
    public function subtract(Cost $other): self
    {
        return self::fromString(bcsub($this->value, $other->value, self::COST_SCALE));
    }

    /** Выравнивание до ровно $scale знаков: "8.4" -> "8.400000". */
    public function toFixedString(int $scale = 6): string
    {
        return $this->roundTo($scale);
    }

    public function valueOf(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    /**
     * Полуканоническое округление до $scale знаков (HALF_UP).
     * Без округления при $nextDigit < 5.
     *
     * Примеры: "0.0000005" -> "0.000001"; "2.5" (scale 0) -> "3"; "8.4" (scale 6) -> "8.400000".
     */
    private function roundTo(int $scale): string
    {
        $negative = str_starts_with($this->value, '-');
        $abs = $negative ? substr($this->value, 1) : $this->value;

        [$intPart, $fracPart] = array_pad(explode('.', $abs, 2), 2, '');
        // гарантируем, что у дробной части есть как минимум scale+1 символ
        $paddedFrac = self::padRight($fracPart, $scale + 1);
        $nextDigit = (int) $paddedFrac[$scale];
        $keepFrac = substr($paddedFrac, 0, $scale);

        // "целое" число в единицах 10^-scale (десятичная строка)
        $digits = ltrim($intPart . $keepFrac, '0');
        if ($nextDigit >= 5) {
            $digits = self::incrementDecimal($digits);
        }

        if ($scale === 0) {
            $out = $digits === '' ? '0' : $digits;
        } else {
            $padded = self::padLeft($digits === '' ? '0' : $digits, $scale + 1);
            $out = substr($padded, 0, -$scale) . '.' . substr($padded, -$scale);
        }

        if ($negative && $out !== '0' . str_repeat('0', $scale) && $out !== '0') {
            return "-{$out}";
        }

        return $out;
    }

    private static function padLeft(string $s, int $totalLen): string
    {
        $n = $totalLen - strlen($s);

        return $n > 0 ? str_repeat('0', $n) . $s : $s;
    }

    private static function padRight(string $s, int $totalLen): string
    {
        $n = $totalLen - strlen($s);

        return $n > 0 ? $s . str_repeat('0', $n) : $s;
    }

    /** Прибавить 1 к неотрицательному целому, записанному десятичной строкой (пустая строка = 0). */
    private static function incrementDecimal(string $digits): string
    {
        if ($digits === '') {
            return '1';
        }

        $trailing = '';
        for ($i = strlen($digits) - 1; $i >= 0; $i--) {
            if ($digits[$i] !== '9') {
                $incremented = (string) ((int) $digits[$i] + 1);

                return substr($digits, 0, $i) . $incremented . $trailing;
            }

            $trailing = '0' . $trailing;
        }

        return '1' . $trailing;
    }
}
