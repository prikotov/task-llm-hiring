<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Стоимость в USD, представленная десятичной строкой (float запрещён).
 */
final readonly class Money
{
    private function __construct(public string $amount)
    {
    }

    public static function zero(): self
    {
        return new self('0');
    }

    public static function of(string $amount): self
    {
        return new self(ltrim($amount, '+'));
    }

    public function plus(self $other): self
    {
        return new self(DecimalMath::add($this->amount, $other->amount));
    }

    public function minus(self $other): self
    {
        return new self(DecimalMath::sub($this->amount, $other->amount));
    }

    /** Умножение на десятичный множитель (оф-пик, коэффициенты). */
    public function scaleBy(string $factor): self
    {
        return new self(DecimalMath::mul($this->amount, $factor));
    }

    /** Единственное допустимое округление: HALF_UP до 6 знаков. */
    public function roundTo6(): self
    {
        return new self(DecimalMath::halfUp6($this->amount));
    }

    public function amount(): string
    {
        return $this->amount;
    }

    public function format6(): string
    {
        return DecimalMath::format6($this->amount);
    }

    public function __toString(): string
    {
        return $this->amount;
    }
}
