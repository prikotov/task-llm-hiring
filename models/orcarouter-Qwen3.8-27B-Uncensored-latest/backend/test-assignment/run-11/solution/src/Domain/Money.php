<?php

declare(strict_types=1);

namespace App\Domain;

use bcmath\Number;

/**
 * Деньги: точная десятичная арифметика (bcmath / bcmath\Number), float запрещён.
 * Значение хранится как десятичная строка с запасом точности (масштаб 24);
 * округление происходит только при явном запросе (roundedToMicros / toFixedSix).
 */
final class Money
{
    public const SCALE = 24;

    /** @var string каноническая десятичная запись */
    private string $amount;

    private function __construct(string $amount)
    {
        $this->amount = self::normalize($amount);
    }

    public static function fromString(string $amount): self
    {
        return new self($amount);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    public function add(self $other): self
    {
        return new self((string) (new Number($this->amount))->add($other->amount));
    }

    public function subtract(self $other): self
    {
        return new self((string) (new Number($this->amount))->sub($other->amount));
    }

    public function multiply(self $other): self
    {
        return new self((string) (new Number($this->amount))->mul($other->amount));
    }

    public function divide(self $other): self
    {
        if ((new Number($other->amount))->compare('0') === 0) {
            throw DivisionByZeroMoneyException::new();
        }

        return new self((string) (new Number($this->amount))->div($other->amount, self::SCALE));
    }

    public function isNegative(): bool
    {
        return str_starts_with($this->amount, '-');
    }

    public function isZero(): bool
    {
        return ltrim($this->amount, '0-') === '';
    }

    public function toInt(): int
    {
        return (int) $this->amount;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * Итог события: ровно одно округление HALF_UP до 6 знаков (2.5 → 3),
     * для отрицательных — «наружу» (−2.5 → −3, т.е. дальше от нуля).
     */
    public function roundedToMicros(): string
    {
        $negative = $this->isNegative();
        $amount = $negative ? substr($this->amount, 1) : $this->amount;

        if (ltrim($amount, '0') === '') {
            return '0.000000';
        }

        // Разбиваем на целую и дробную части
        $dot = strpos($amount, '.');
        if ($dot === false) {
            $intPart = $amount;
            $fracPart = '';
        } else {
            $intPart = substr($amount, 0, $dot);
            $fracPart = substr($amount, $dot + 1);
        }

        // Дробная часть в 7 символах (7-я позиция — решающая; index от 0)
        $fracPadded = str_pad($fracPart, 7, '0');
        $seventh = (int) $fracPadded[6];
        $first6 = substr($fracPadded, 0, 6);
        $carry = $seventh >= 5 ? 1 : 0;

        // micros = intPart * 10^6 + int(first6) + carry
        // intPart * 10^6
        $intPartClean = ltrim($intPart, '0') ?: '0';
        $micros = $intPartClean . $first6;

        if ($carry > 0) {
            $micros = (string) (new Number($micros))->add('1');
        }

        $micros = ltrim($micros, '0') ?: '0';

        // Форматируем как X.YYYYZZ
        if (strlen($micros) <= 6) {
            $micros = str_pad($micros, 6, '0', STR_PAD_LEFT);
            $formatted = '0.' . $micros;
        } else {
            $intOut = substr($micros, 0, -6);
            $fracOut = substr($micros, -6);
            $formatted = $intOut . '.' . $fracOut;
        }

        return ($negative ? '-' : '') . $formatted;
    }

    /**
     * Строка ровно с 6 знаками после точки (для отчёта).
     * Допустимый вход: целые либо числа, не длиннее 6 знаков после точки.
     */
    public function toFixedSix(): string
    {
        $negative = $this->isNegative();
        $amount = $negative ? substr($this->amount, 1) : $this->amount;

        if (ltrim($amount, '0') === '') {
            return '0.000000';
        }

        $dot = strpos($amount, '.');
        if ($dot === false) {
            $intPart = ltrim($amount, '0') ?: '0';
            $fracPart = '';
        } else {
            $intPart = ltrim(substr($amount, 0, $dot), '0') ?: '0';
            $fracPart = substr($amount, $dot + 1);
        }

        $frac = str_pad(substr($fracPart, 0, 6), 6, '0');

        return ($negative ? '-' : '') . $intPart . '.' . $frac;
    }

    public function __toString(): string
    {
        return $this->amount;
    }

    private static function normalize(string $value): string
    {
        if (!preg_match('/^[+-]?(\d+)(\.\d+)?$/', $value)) {
            throw InvalidMoneyValueException::new($value);
        }

        $negative = str_starts_with($value, '-');
        $value = ltrim($value, '+-');

        if (str_contains($value, '.')) {
            [$int, $frac] = explode('.', $value, 2);
            $frac = rtrim($frac, '0');
            $value = $frac === '' ? $int : $int . '.' . $frac;
        }
        $value = preg_replace('/^0+(?=\d)/', '', $value) ?? '0';

        return ($negative && $value !== '0' ? '-' : '') . $value;
    }
}
