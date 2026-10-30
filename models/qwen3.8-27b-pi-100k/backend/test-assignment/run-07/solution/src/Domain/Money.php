<?php

declare(strict_types=1);

namespace App\Domain;

use InvalidArgumentException;

/**
 * Точная десятичная арифметика для денег (bcmath, float запрещён).
 *
 * Промежуточные значения ведутся с запасной точностью SCALE (>= 16 знаков)
 * без округления; единственное округление — явный вызов roundHalfUp(),
 * ровно одно — на итоговой стоимости события (правило 4 задания).
 *
 * Все суммы здесь неотрицательные (стоимость событий); отрицательные
 * значения поддерживаются в арифметике, но HALF_UP для них — от нуля
 * (стандартное «половина вверх от нуля»).
 */
final class Money
{
    public const SCALE = 20;

    private const ONE_MILLION = '1000000';

    /**
     * @param string $amount bcmath-строка, напр. «14.26249875»
     */
    private function __construct(private readonly string $amount)
    {
    }

    /**
     * @throws InvalidArgumentException при недопустимом десятичном значении
     */
    public static function fromDecimal(string $amount): self
    {
        if (preg_match('/^-?\d+(?:\.\d+)?$/', $amount) !== 1) {
            throw new InvalidArgumentException("Некорректное десятичное значение: {$amount}");
        }

        return new self($amount);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    public function add(self $other): self
    {
        return new self(bcadd($this->amount, $other->amount, self::SCALE));
    }

    /**
     * @param string $factor десятичная строка (напр. ставка, множитель)
     */
    public function multiplyBy(string $factor): self
    {
        if (preg_match('/^-?\d+(?:\.\d+)?$/', $factor) !== 1) {
            throw new InvalidArgumentException("Некорректный множитель: {$factor}");
        }

        return new self(bcmul($this->amount, $factor, self::SCALE));
    }

    /**
     * Деление на 1 000 000 (пересчёт «цена за 1M токенов»).
     */
    public function perMillion(): self
    {
        return new self(bcdiv($this->amount, self::ONE_MILLION, self::SCALE));
    }

    /**
     * Единственное допустимое округление: HALF_UP до `$scale` знаков
     * (по умолчанию 6). Половина округляется «вверх» (от нуля):
     * 14.26249875 → 14.262499, «2.5» со scale 0 → 3.
     */
    public function roundHalfUp(int $scale = 6): self
    {
        if ($scale < 0) {
            throw new InvalidArgumentException('Точность не может быть отрицательной');
        }

        $negative = str_starts_with($this->amount, '-');
        $abs = $negative ? substr($this->amount, 1) : $this->amount;
        $factor = '1' . str_repeat('0', $scale);

        // Точное умножение на 10^scale: дробный остаток значения <= SCALE,
        // после сдвига — не более SCALE-1 знаков, scale=SCALE сохраняет всё.
        $scaled = bcmul($abs, $factor, self::SCALE);
        $integer = bcdiv($scaled, '1', 0); // отбрасывание дробной части

        if (bccomp(bcsub($scaled, $integer, self::SCALE), '0.5', self::SCALE) >= 0) {
            $integer = bcadd($integer, '1', 0);
        }

        $result = bcdiv($integer, $factor, $scale);
        if ($negative && bccomp($result, '0', $scale) !== 0) {
            $result = '-' . $result;
        }

        return new self($result);
    }

    /**
     * Представление ровно с `$scale` знаками после точки (без округления —
     * только форматирование), например для вывода 6 знаков в отчёте.
     */
    public function format(int $scale = 6): string
    {
        return bcadd($this->amount, '0', $scale);
    }

    public function toString(): string
    {
        return $this->amount;
    }
}
