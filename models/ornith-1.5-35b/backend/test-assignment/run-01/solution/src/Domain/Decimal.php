<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Обертка над bcmath для точной десятичной арифметики денег.
 *
 * Float для денег запрещён требованиями задания — все операции идут
 * через строковые представления с заданной точностью.
 *
 * Все денежные величины здесь неотрицательны (количества токенов, ставки,
 * множители неотрицательны), поэтому HALF_UP эмулируется режимом
 * PHP_ROUND_HALF_UP встроенной функции bcround; bcmath не округляет
 * промежуточные bcmul/bcadd/bcdiv, поэтому округляется только итог.
 */
final class Decimal
{
    public const ROUND_SCALE = 6;
    public const COMPUTE_SCALE = 16;

    private string $value;

    public function __construct(string $value)
    {
        $this->value = trim($value) === '' ? '0' : trim($value);
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public static function fromInt(int $value): self
    {
        return new self((string) $value);
    }

    public function add(string $operand): self
    {
        return new self(bcadd($this->value, $operand, self::COMPUTE_SCALE));
    }

    public function mul(string $operand): self
    {
        return new self(bcmul($this->value, $operand, self::COMPUTE_SCALE));
    }

    public function div(string $operand): self
    {
        return new self(bcdiv($this->value, $operand, self::COMPUTE_SCALE));
    }

    /**
     * Единственное округление в конвейере: до 6 знаков по HALF_UP.
     */
    public function round(int $scale = self::ROUND_SCALE): self
    {
        return new self(self::roundValue($this->value, $scale));
    }

    /**
     * Округление строки до $scale знаков по HALF_UP (HALF AWAY FROM ZERO).
     *
     * bcround поддерживает HALF_EVEN по умолчанию; режим
     * PHP_ROUND_HALF_UP даёт требуемое поведение для неотрицательных
     * значений, которыми являются все денежные суммы в проекте.
     *
     * @return string округлённое значение (без лишних ведущих нулей)
     */
    public static function roundValue(string $value, int $scale): string
    {
        return bcround($value, $scale, \RoundingMode::HalfAwayFromZero);
    }

    public function toString(): string
    {
        return $this->value;
    }

    public function formatScaled(int $scale): string
    {
        return number_format((float) $this->value, $scale, '', '');
    }

    public function equals(string $operand): bool
    {
        return bccomp($this->value, $operand, self::COMPUTE_SCALE) === 0;
    }
}
