<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use RuntimeException;

/**
 * Точное десятичное значение денежной суммы (бессимвольный bcmath).
 * Float для денег запрещён: все операции — на строках через bcmath.
 */
final readonly class Money
{
    /** Рабочий масштаб промежуточных вычислений (>= 16 по требованию задания). */
    public const SCALE = 24;

    /** Кол-во знаков для итоговых сумм (округление событий, отчёты, дельты). */
    public const DECIMALS = 6;

    /** Значение единицы по схеме per_1m_*: цена задаётся за 1 000 000 токенов. */
    public const UNIT_TOKENS = '1000000';

    private string $amount;

    public function __construct(string $amount)
    {
        if (!preg_match('/^[+-]?\d+(\.\d+)?$/', $amount)) {
            throw new RuntimeException(sprintf('Invalid decimal amount "%s".', $amount));
        }
        $this->amount = self::normalize($amount);
    }

    public function amount(): string
    {
        return $this->amount;
    }

    public function add(Money $other): Money
    {
        return new Money(bcadd($this->amount, $other->amount, self::SCALE));
    }

    public function subtract(Money $other): Money
    {
        return new Money(bcsub($this->amount, $other->amount, self::SCALE));
    }

    /**
     * Умножение на десятичный множитель без округления.
     */
    public function multiplyBy(string $decimal): Money
    {
        $amount = bcmul($this->amount, $decimal, self::SCALE);
        return new Money($amount);
    }

    /**
     * Деление без округления при заданном масштабе.
     */
    public function divideBy(string $decimal, int $scale = self::SCALE): Money
    {
        if (bccomp($decimal, '0', self::SCALE) === 0) {
            throw new RuntimeException('Division by zero.');
        }
        return new Money(bcdiv($this->amount, $decimal, $scale));
    }

    /**
     * Округление ровно до 6 знаков после точки по правилу HALF_UP (2.5 -> 3).
     */
    public function roundTo6(): Money
    {
        return new Money(self::roundHalfUp($this->amount, self::DECIMALS));
    }

    public function isZero(): bool
    {
        return bccomp($this->amount, '0', self::SCALE) === 0;
    }

    public function __toString(): string
    {
        return $this->amount;
    }

    /**
     * Форматирует сумму в строку ровно с 6 знаками после точки (HALF_UP).
     */
    public function format(): string
    {
        return self::roundHalfUp($this->amount, self::DECIMALS);
    }

    private static function normalize(string $amount): string
    {
        $result = bcadd($amount, '0', self::SCALE);
        if (str_starts_with($result, '-0.')) {
            $result = substr($result, 1);
        }
        return $result;
    }

    /**
     * HALF_UP: отбрасываем все знаки после $decimals, если отброшенная часть
     * >= половины единицы — прибираем 1.
     */
    private static function roundHalfUp(string $amount, int $decimals): string
    {
        $parts = explode('.', $amount, 2);
        $int = $parts[0];
        $frac = $parts[1] ?? '';
        $sign = '';
        if (str_starts_with($int, '-')) {
            $sign = '-';
            $int = substr($int, 1);
        }
        $frac = preg_replace('/0+$/', '', $frac) ?? '';

        $keep = substr($frac, 0, $decimals);
        $rest = substr($frac, $decimals);

        if (strlen($keep) < $decimals) {
            $keep = str_pad($keep, $decimals, '0');
        }

        $carry = self::restAtLeastHalf($rest);
        if ($carry) {
            $digits = $int . $keep;
            $carryDigit = '1';
            $pos = strlen($digits) - 1;
            while ($carryDigit !== null && $pos >= 0) {
                $sum = (int) $digits[$pos] + 1;
                $digits[$pos] = (string) ($sum % 10);
                $carryDigit = $sum >= 10 ? '1' : null;
                $pos--;
            }
            if ($carryDigit !== null) {
                $digits = $carryDigit . $digits;
            }
            $int = substr($digits, 0, strlen($digits) - $decimals) ?: '0';
            $keep = substr($digits, strlen($digits) - $decimals) ?: str_repeat('0', $decimals);
        }

        return $sign . $int . '.' . str_pad($keep, $decimals, '0');
    }

    private static function restAtLeastHalf(string $rest): bool
    {
        if ($rest === '') {
            return false;
        }
        if ((int) $rest[0] > 5) {
            return true;
        }
        if ((int) $rest[0] < 5) {
            return false;
        }
        // ровно половина: если в остатке есть ненулевой знак
        for ($i = 1; $i < strlen($rest); $i++) {
            if ($rest[$i] !== '0') {
                return true;
            }
        }

        return true; // ровно половина -> "up"
    }
}
