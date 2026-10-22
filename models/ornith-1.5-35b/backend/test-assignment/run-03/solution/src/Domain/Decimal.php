<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Обёртка над bcmath для точной десятичной арифметки денег.
 *
 * Промежуточные вычисления выполняются без округления в масштабе INPUT_SCALE.
 * Округление итоговой стоимости события — до COST_SCALE знаков по HALF_UP.
 */
final class Decimal
{
    /** промежуточная точность bcmath. */
    public const INPUT_SCALE = 16;

    /** округление итоговой стоимости события/суммы отчёта. */
    public const COST_SCALE = 6;

    private readonly string $value;

    public function __construct(string $value)
    {
        $this->value = $value;
    }

    public static function parse(int|string $value): self
    {
        return new self((string) $value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function add(int|string $other): self
    {
        $sum = bcadd($this->value, (string) $other, self::INPUT_SCALE);

        return new self($sum);
    }

    public function subtract(int|string $other): self
    {
        $diff = bcsub($this->value, (string) $other, self::INPUT_SCALE);

        return new self($diff);
    }

    public function multiply(int|string $other): self
    {
        $product = bcmul($this->value, (string) $other, self::INPUT_SCALE);

        return new self($product);
    }

    public function divide(int|string $other): self
    {
        $quotient = bcdiv($this->value, (string) $other, self::INPUT_SCALE);

        return new self($quotient);
    }

    /**
     * HALF_UP округление десятичной строки до заданного числа знаков.
     * Дробная часть рассматывается как неотрицательное целое число.
     *
     * @param string|int|float $number
     */
    public static function halfUp($number, int $scale = self::COST_SCALE): string
    {
        $sign = '';
        $abs = (string) $number;
        if (str_starts_with($abs, '-')) {
            $sign = '-';
            $abs = substr($abs, 1);
        }

        [$intPart, $fracPart] = array_pad(explode('.', $abs, 2), 2, '');
        $intPart = $intPart === '' ? '0' : $intPart;
        $fracPart = str_pad($fracPart, $scale, '0');

        if (strlen($fracPart) > $scale) {
            $rest = (int) substr($fracPart, $scale, 1);
            $fracPart = substr($fracPart, 0, $scale);
            if ($rest >= 5) {
                $fracPart = bcadd($fracPart, '1', 0);
                if (strlen($fracPart) > $scale) {
                    // переполнение: 9999...9 -> 0000...0 с инкремтом целой части
                    $intPart = bcadd($intPart, '1', 0);
                    $fracPart = str_repeat('0', $scale);
                }
            }
        }

        return $scale === 0 ? $sign . $intPart : $sign . $intPart . '.' . $fracPart;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
