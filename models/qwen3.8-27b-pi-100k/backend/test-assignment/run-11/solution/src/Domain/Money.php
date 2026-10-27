<?php

declare(strict_types=1);

namespace App\Domain;

use App\Application\Exception\BillingException;

/**
 * Немутабельная десятичная величина (аналог money) с точностью, заданной
 * десятичной строкой. НЕ float — float запрещён для денег (TASK.md).
 *
 * Внутри хранится строка вида "-1.25", "0", "10" (без float-погрешностей).
 *
 * Поддерживаемые операции: add, subtract, multiply, divide, round (HALF_UP),
 * compareTo, format.
 */
final class Money
{
    /** Внутренний масштаб бcmath для всех промежуточных операций. */
    private const SCALE = 32;

    private function __construct(private readonly string $value)
    {
    }

    /**
     * @param string|int $value — десятичная строка ('2.50') или целое.
     *                           НЕ float — float запрещён (TASK.md).
     */
    public static function of(string|int $value): self
    {
        if (is_float($value)) {
            throw BillingException::invalidValue('float запрещён для денег');
        }
        $s = trim((string) $value);
        if ($s === '' || $s === '+' || $s === '-') {
            throw BillingException::invalidValue('пустое значение');
        }
        $s = ltrim($s, '+');
        if (!preg_match('/^-?(0|[1-9]\d*)?(\.\d+)?$/', $s) && !preg_match('/^-?(0|[1-9]\d*)?(\.\d*)?$/', $s)) {
            throw BillingException::invalidValue(sprintf('недопустимое десятичное значение: "%s"', $value));
        }
        return new self(self::normalize($s));
    }

    public static function zero(): self
    {
        return new self('0');
    }

    public function add(self $other): self
    {
        return new self(self::normalize(bcadd($this->value, $other->value, self::SCALE)));
    }

    public function subtract(self $other): self
    {
        return new self(self::normalize(bcsub($this->value, $other->value, self::SCALE)));
    }

    public function multiply(self $other): self
    {
        return new self(self::normalize(bcmul($this->value, $other->value, self::SCALE)));
    }

    public function divide(self $other): self
    {
        if ($other->isZero()) {
            throw BillingException::invalidValue('Деление на ноль');
        }
        return new self(self::normalize(bcdiv($this->value, $other->value, self::SCALE)));
    }

    public function isZero(): bool
    {
        return $this->value === '0';
    }

    public function isNegative(): bool
    {
        return str_starts_with($this->value, '-');
    }

    /**
     * HALF_UP округление до scale знаков (2.5 → 3, -2.5 → -3).
     */
    public function round(int $scale = 6): self
    {
        if ($scale < 0) {
            throw BillingException::invalidValue('scale должно быть >= 0');
        }
        $neg = str_starts_with($this->value, '-');
        $num = $neg ? substr($this->value, 1) : $this->value;

        $dot = strpos($num, '.');
        $int = $dot === false ? $num : substr($num, 0, $dot);
        $frac = $dot === false ? '' : substr($num, $dot + 1);
        $frac = str_pad($frac, $scale + 1, '0');

        $keep = substr($frac, 0, $scale);
        $drop = (int) $frac[$scale];

        if ($drop >= 5) {
            // Добавляем 1 к последней цифре keep (в строковом виде, без float).
            if ($scale === 0) {
                // scale=0: нужно прибавить 1 к integer part
                $intDigits = str_split($int === '' ? '0' : $int);
                $c2 = 1;
                for ($i = count($intDigits) - 1; $i >= 0; $i--) {
                    $sum = (int) $intDigits[$i] + $c2;
                    $intDigits[$i] = (string) ($sum % 10);
                    $c2 = intdiv($sum, 10);
                    if ($c2 === 0) {
                        break;
                    }
                }
                if ($c2 > 0) {
                    array_unshift($intDigits, '1');
                }
                $int = implode('', $intDigits);
            } else {
                // Добавляем 1 к последней цифре $keep (в строковом виде, без float).
                $digits = str_split($keep === '' ? '0' : $keep);
                $carry = 1;
                for ($i = count($digits) - 1; $i >= 0; $i--) {
                    $sum = (int) $digits[$i] + $carry;
                    $digits[$i] = (string) ($sum % 10);
                    $carry = intdiv($sum, 10);
                    if ($carry === 0) {
                        break;
                    }
                }
                $keep = implode('', $digits);
                // Если перенос дошёл за пределы keep — нужно увеличить integer part.
                if ($carry > 0) {
                    $intDigits = str_split($int === '' ? '0' : $int);
                    $c2 = 1;
                    for ($i = count($intDigits) - 1; $i >= 0; $i--) {
                        $sum = (int) $intDigits[$i] + $c2;
                        $intDigits[$i] = (string) ($sum % 10);
                        $c2 = intdiv($sum, 10);
                        if ($c2 === 0) {
                            break;
                        }
                    }
                    if ($c2 > 0) {
                        array_unshift($intDigits, '1');
                    }
                    $int = implode('', $intDigits);
                }
            }
        }

        $int = ltrim($int, '0');
        if ($int === '') {
            $int = '0';
        }
        if ($scale === 0) {
            $result = $int;
        } else {
            $fracTrim = rtrim($keep, '0');
            $result = $fracTrim === '' ? $int : $int . '.' . $fracTrim;
        }

        return new self($neg && $result !== '0' ? '-' . $result : $result);
    }

    /** Строки ровно $scale знаков после точки. */
    public function format(int $scale = 6): string
    {
        $neg = str_starts_with($this->value, '-');
        $num = $neg ? substr($this->value, 1) : $this->value;
        $dot = strpos($num, '.');
        $int = $dot === false ? $num : substr($num, 0, $dot);
        $frac = $dot === false ? '' : substr($num, $dot + 1);
        $frac = substr(str_pad($frac, $scale, '0'), 0, $scale);
        $int = ltrim($int, '0');
        if ($int === '') {
            $int = '0';
        }
        if ($scale === 0) {
            return ($neg ? '-' : '') . $int;
        }
        return ($neg ? '-' : '') . $int . '.' . $frac;
    }

    public function toDecimalString(): string
    {
        return $this->value;
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function compareTo(self $other): int
    {
        $r = bccomp($this->value, $other->value, self::SCALE);
        return $r < 0 ? -1 : ($r > 0 ? 1 : 0);
    }

    public function equals(self $other): bool
    {
        return $this->compareTo($other) === 0;
    }

    /** Приводим к виду без лишних нулей: "001.2300" → "1.23". */
    private static function normalize(string $s): string
    {
        $neg = str_starts_with($s, '-');
        $num = $neg ? substr($s, 1) : $s;
        $dot = strpos($num, '.');
        if ($dot !== false) {
            $int = substr($num, 0, $dot);
            $frac = substr($num, $dot + 1);
            $int = ltrim($int, '0');
            if ($int === '') {
                $int = '0';
            }
            $frac = rtrim($frac, '0');
            if ($frac === '') {
                return ($neg ? '-' : '') . $int;
            }
            return ($neg ? '-' : '') . $int . '.' . $frac;
        }
        $int = ltrim($num, '0');
        if ($int === '') {
            return '0';
        }
        return ($neg ? '-' : '') . $int;
    }
}
