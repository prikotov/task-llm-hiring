<?php

declare(strict_types=1);

namespace App\Shared;

use InvalidArgumentException;
use RoundingMode;

/**
 * Неизменяемое десятичное число поверх bcmath. Float не используется нигде.
 */
final readonly class Decimal
{
    /** Масштаб промежуточных вычислений (без округления в пределах этого запаса). */
    public const int SCALE = 24;

    private function __construct(public string $value)
    {
    }

    public static function of(string|int $value): self
    {
        $value = (string) $value;
        if (preg_match('/^-?\d+(\.\d+)?$/', $value) !== 1) {
            throw new InvalidArgumentException(sprintf('Некорректное десятичное число: "%s"', $value));
        }

        return new self($value);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    public function add(self $o): self
    {
        return new self(bcadd($this->value, $o->value, self::SCALE));
    }

    public function sub(self $o): self
    {
        return new self(bcsub($this->value, $o->value, self::SCALE));
    }

    public function mul(self $o): self
    {
        return new self(bcmul($this->value, $o->value, self::SCALE));
    }

    public function div(self $o): self
    {
        return new self(bcdiv($this->value, $o->value, self::SCALE));
    }

    public function isNegative(): bool
    {
        return bccomp($this->value, '0', self::SCALE) < 0;
    }

    public function equals(self $o): bool
    {
        return bccomp($this->value, $o->value, self::SCALE) === 0;
    }

    /** Округление HALF_UP (от нуля) до $scale знаков. */
    public function round(int $scale): self
    {
        return new self(bcround($this->value, $scale, RoundingMode::HalfAwayFromZero));
    }

    /** Строка ровно с $scale знаками после точки (значение должно быть уже округлено). */
    public function toFixed(int $scale): string
    {
        return bcadd(bcround($this->value, $scale, RoundingMode::HalfAwayFromZero), '0', $scale);
    }
}
