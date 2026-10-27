<?php

declare(strict_types=1);

namespace App\Billing\ValueObject;

use InvalidArgumentException;

/**
 * Инвариантно? Нет — неизменяемое десятичное число на bcmath.
 *
 * Все арифметические операции выполняются с фиксированным большим масштабом
 * (без округления результата). Явное округление применяется единственным
 * допустимым способом — через round() (HALF_UP), как того требует правило 4.
 *
 * Тип float здесь не используется.
 */
final class Decimal
{
    /** Масштаб промежуточных вычислений: более чем запас под «rate × multiplier». */
    public const SCALE = 40;

    /** Нормализованная десятичная строка: опциональный знак, целая часть, необязательная дробная (без хвостовых нулей). */
    private string $value;

    public function __construct(string $value)
    {
        if (preg_match('/^[+-]?\d+(\.\d+)?$/', $value) !== 1) {
            throw new InvalidArgumentException(sprintf('Некорректная десятичная строка: "%s".', $value));
        }

        $normalized = $value;
        if (str_contains($normalized, '.')) {
            $normalized = rtrim($normalized, '0');
            if (str_ends_with($normalized, '.')) {
                $normalized = substr($normalized, 0, -1);
            }
        }
        // «-0» → «0»
        if (str_replace(['-', '.', '0'], '', $normalized) === '') {
            $normalized = '0';
        }
        $this->value = $normalized;
    }

    public static function of(int|string $value): self
    {
        return new self((string) $value);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    public function add(self $other): self
    {
        return new self(bcadd($this->value, $other->value, self::SCALE));
    }

    public function sub(self $other): self
    {
        return new self(bcsub($this->value, $other->value, self::SCALE));
    }

    public function mul(self $other): self
    {
        return new self(bcmul($this->value, $other->value, self::SCALE));
    }

    public function div(self $other): self
    {
        return new self(bcdiv($this->value, $other->value, self::SCALE));
    }

    /**
     * Округление до $digits знаков после точки, HALF_UP (2.5 → 3 при 0 знаках,
     * -2.5 → -3), реализованное строковой арифметикой bcmath — без bcround()
     * (deprecated в bcmath 2.0 / PHP 8.4+) и без float.
     */
    public function round(int $digits): self
    {
        if ($digits < 0) {
            throw new InvalidArgumentException('Число знаков не может быть отрицательным.');
        }

        $sign = str_starts_with($this->value, '-') ? '-' : '';
        $abs = $sign === '' ? $this->value : substr($this->value, 1);

        $dot = strpos($abs, '.');
        $intPart = $dot === false ? $abs : substr($abs, 0, $dot);
        $fracPart = $dot === false ? '' : substr($abs, $dot + 1);

        $kept = str_pad(substr($fracPart, 0, $digits), $digits, '0', STR_PAD_RIGHT);
        $next = $digits < strlen($fracPart) ? $fracPart[$digits] : null;

        // «10^digits-целое» представление: все цифры значения как одна строка (дробные дополнены нулями),
        // прибавка 1 = прибавление 10^-$digits.
        $combined = (ltrim($intPart, '0') ?: '0').$kept;
        if ($next !== null && (int) $next >= 5) {
            $combined = bcadd($combined, '1', 0);
        }
        $combined = str_pad($combined, $digits, '0', STR_PAD_LEFT);

        $intOut = substr($combined, 0, \max(1, strlen($combined) - $digits));
        $fracOut = $digits === 0 ? '' : substr($combined, strlen($combined) - $digits);

        $result = $intOut.($fracOut === '' ? '' : '.'.$fracOut);

        return new self($sign.$result);
    }

    public function negated(): self
    {
        return new self(bcmul($this->value, '-1', self::SCALE));
    }

    public function isZero(): bool
    {
        return $this->value === '0';
    }

    /** Строка с точностью ровно до знаков: ровно $digits знаков после точки (в т.ч. нулей). */
    public function toFixed(int $digits): string
    {
        return bcadd($this->value, '0', $digits);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
