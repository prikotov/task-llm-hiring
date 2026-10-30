<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use InvalidArgumentException;
use RoundingMode;

/**
 * Точное десятичное число поверх bcmath.
 *
 * Все операции выполняются с запасом точности (SCALE знаков после точки),
 * что для наших величин (целые токены × ставки ≤ 12 знаков × множитель)
 * означает отсутствие потерь. Округление — только явное, через roundHalfUp().
 * float не используется нигде.
 */
final readonly class Decimal
{
    private const int SCALE = 40;
    private const string PATTERN = '/^[+-]?\d+(\.\d+)?$/';

    private function __construct(private string $value)
    {
    }

    public static function of(string $value): self
    {
        $value = trim($value);
        if (preg_match(self::PATTERN, $value) !== 1) {
            throw new InvalidArgumentException(sprintf('"%s" не является десятичным числом.', $value));
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

    /** Количество знаков после точки во входной строке (без нормализации). */
    public static function fractionDigits(string $value): int
    {
        $dot = strpos($value, '.');

        return $dot === false ? 0 : strlen($value) - $dot - 1;
    }

    public function add(self $other): self
    {
        return new self(self::normalize(bcadd($this->value, $other->value, self::SCALE)));
    }

    public function sub(self $other): self
    {
        return new self(self::normalize(bcsub($this->value, $other->value, self::SCALE)));
    }

    public function mul(self $other): self
    {
        return new self(self::normalize(bcmul($this->value, $other->value, self::SCALE)));
    }

    public function div(self $other): self
    {
        if ($other->isZero()) {
            throw new InvalidArgumentException('Деление на ноль.');
        }

        return new self(self::normalize(bcdiv($this->value, $other->value, self::SCALE)));
    }

    /** Округление HALF_UP (половина — от нуля): 2.5 → 3, -2.5 → -3. */
    public function roundHalfUp(int $scale): self
    {
        return new self(self::normalize(bcround($this->value, $scale, RoundingMode::HalfAwayFromZero)));
    }

    public function compare(self $other): int
    {
        return bccomp($this->value, $other->value, self::SCALE);
    }

    public function equals(self $other): bool
    {
        return $this->compare($other) === 0;
    }

    public function isZero(): bool
    {
        return $this->compare(self::zero()) === 0;
    }

    public function isNegative(): bool
    {
        return $this->compare(self::zero()) < 0;
    }

    /**
     * Строка ровно с $scale знаками после точки.
     *
     * Если число содержит больше значащих знаков, чем $scale, — это ошибка
     * вызывающего кода (молчаливого округления быть не должно).
     */
    public function toFixed(int $scale): string
    {
        if (!$this->roundHalfUp($scale)->equals($this)) {
            throw new InvalidArgumentException(sprintf('Число %s не представимо с %d знаками без округления.', $this->value, $scale));
        }

        return bcadd($this->value, '0', $scale);
    }

    /** Каноническое представление: без лишних нулей, "-0" → "0". */
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
        $negative = str_starts_with($value, '-');
        $value = ltrim($value, '+-');

        if (str_contains($value, '.')) {
            $value = rtrim(rtrim($value, '0'), '.');
        }
        $value = ltrim($value, '0');
        if ($value === '' || str_starts_with($value, '.')) {
            $value = '0'.$value;
        }

        return ($negative && $value !== '0') ? '-'.$value : $value;
    }
}
