<?php

declare(strict_types=1);

namespace App\Domain\Money;

use App\Domain\Money\Exception\InvalidAmount;

use function bcadd;
use function bccomp;
use function bcsub;
use function bcdiv;
use function bcmul;
use function rtrim;
use function str_contains;
use function str_repeat;
use function str_starts_with;
use function substr;
use function trim;

/**
 * Неизменяемое десятичное число поверх bcmath.
 *
 * Единственный тип для денег в домене: float не используется нигде.
 * Внутренние вычисления ведутся с большим запасом точности, округление
 * выполняется только явно (см. roundHalfUp()).
 */
final readonly class Decimal
{
    /** Рабочая точность промежуточных вычислений. */
    private const CALC_SCALE = 30;

    private function __construct(private string $value)
    {
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);

        if (1 !== preg_match('/^[+-]?(\d+(\.\d+)?|\.\d+)$/', $value)) {
            throw InvalidAmount::forValue($value);
        }

        return new self(self::normalize(bcadd($value, '0', self::CALC_SCALE)));
    }

    public static function fromInt(int $value): self
    {
        return new self((string) $value);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    /**
     * @param list<self> $values
     */
    public static function sum(array $values): self
    {
        $sum = self::zero();
        foreach ($values as $value) {
            $sum = $sum->add($value);
        }

        return $sum;
    }

    public function add(self $other): self
    {
        return new self(self::normalize(bcadd($this->value, $other->value, self::CALC_SCALE)));
    }

    public function sub(self $other): self
    {
        return new self(self::normalize(bcsub($this->value, $other->value, self::CALC_SCALE)));
    }

    public function mul(self $other): self
    {
        return new self(self::normalize(bcmul($this->value, $other->value, self::CALC_SCALE)));
    }

    public function div(self $other): self
    {
        if ($other->isZero()) {
            throw InvalidAmount::divisionByZero();
        }

        return new self(self::normalize(bcdiv($this->value, $other->value, self::CALC_SCALE)));
    }

    public function compare(self $other): int
    {
        return bccomp($this->value, $other->value, self::CALC_SCALE);
    }

    public function equals(self $other): bool
    {
        return 0 === $this->compare($other);
    }

    public function isZero(): bool
    {
        return 0 === bccomp($this->value, '0', self::CALC_SCALE);
    }

    /**
     * Округление до $scale знаков по правилу HALF_UP (2.5 -> 3,
     * -2.5 -> -3, т.е. «от нуля»). Это единственное место, где домен
     * меняет точность величины.
     */
    public function roundHalfUp(int $scale): self
    {
        if ($scale < 0) {
            throw InvalidAmount::forValue((string) $scale);
        }

        $negative = str_starts_with($this->value, '-');
        $absolute = $negative ? substr($this->value, 1) : $this->value;

        // Добавляем половину младшего разряда и отбрасываем хвост:
        // bcadd() усекает до запрошенного масштаба, что и даёт HALF_UP
        // для неотрицательной величины.
        $half = 0 === $scale ? '0.5' : '0.' . str_repeat('0', $scale) . '5';
        $rounded = self::normalize(bcadd($absolute, $half, $scale));

        if ($negative && !$this->isZeroString($rounded)) {
            return new self('-' . $rounded);
        }

        return new self($rounded);
    }

    /**
     * Строковое представление ровно с $scale знаками после точки.
     * Значение предварительно округляется HALF_UP, поэтому метод
     * безопасен и как форматтер для уже округлённых денег.
     */
    public function fixed(int $scale): string
    {
        $rounded = $this->roundHalfUp($scale)->value;
        $negative = str_starts_with($rounded, '-');
        $absolute = $negative ? substr($rounded, 1) : $rounded;

        $formatted = bcadd($absolute, '0', $scale);
        if ($scale > 0 && !str_contains($formatted, '.')) {
            $formatted .= '.' . str_repeat('0', $scale);
        }

        return ($negative && !$this->isZeroString($formatted) ? '-' : '') . $formatted;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    private static function normalize(string $value): string
    {
        if (str_contains($value, '.')) {
            $value = rtrim(rtrim($value, '0'), '.');
        }

        if ('' === $value || '-' === $value || '-0' === $value) {
            return '0';
        }

        return $value;
    }

    private function isZeroString(string $value): bool
    {
        return 0 === bccomp($value, '0', self::CALC_SCALE);
    }
}
