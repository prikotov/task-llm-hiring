<?php

declare(strict_types=1);

namespace App\Domain;

use DomainException;
use InvalidArgumentException;

/**
 * Немутабельное десятичное число на базе bcmath — для денег и ставок.
 *
 * Тип float запрещён: все операции — точная десятичная арифметика.
 * Рабочий масштаб (30 знаков) превышает требуемый минимум (16),
 * что даёт запас точности для промежуточных вычислений.
 */
final class Decimal
{
    private const SCALE = 30;

    private function __construct(
        private readonly string $value,
    ) {
    }

    public static function of(string|int|self $value): self
    {
        if ($value instanceof self) {
            return $value;
        }

        if (is_int($value)) {
            $value = (string) $value;
        }

        if (preg_match('/^-?(\d+\.?\d*|\.\d+)$/', $value) !== 1) {
            throw new InvalidArgumentException(sprintf('Некорректное десятичное число: "%s".', $value));
        }

        return new self(bcadd($value, '0', self::SCALE));
    }

    /** Ставка (или стоимость), отсутствующая в данных — программная ошибка. */
    public static function required(?string $value, string $field): self
    {
        if ($value === null || $value === '') {
            throw new DomainException(sprintf('Значение поля "%s" не задано.', $field));
        }

        return self::of($value);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    /** Нормализованная строка (возможные хвостовые нули до рабочего масштаба). */
    public function value(): string
    {
        return $this->value;
    }

    public function add(Decimal|string|int $other): self
    {
        return new self(bcadd($this->value, self::valueOf($other), self::SCALE));
    }

    public function sub(Decimal|string|int $other): self
    {
        return new self(bcsub($this->value, self::valueOf($other), self::SCALE));
    }

    public function mul(Decimal|string|int $other): self
    {
        return new self(bcmul($this->value, self::valueOf($other), self::SCALE));
    }

    public function div(Decimal|string|int $other): self
    {
        if (bccomp(self::valueOf($other), '0', self::SCALE) === 0) {
            throw new DomainException('Деление на ноль.');
        }

        return new self(bcdiv($this->value, self::valueOf($other), self::SCALE));
    }

    public function isZero(): bool
    {
        return bccomp($this->value, '0', self::SCALE) === 0;
    }

    public function equals(Decimal|string|int $other): bool
    {
        return bccomp($this->value, self::valueOf($other), self::SCALE) === 0;
    }

    public function isNegative(): bool
    {
        return $this->value[0] === '-';
    }

    /**
     * Округление до $places знаков после точки, HALF_UP
     * ("5 и дальше — вверх", отрицательные — от нуля; 2.5 → 3).
     */
    public function roundHalfUp(int $places = 6): self
    {
        if ($places < 0 || $places > self::SCALE) {
            throw new InvalidArgumentException(sprintf('Недопустимое число знаков: %d.', $places));
        }

        $negative = $this->value[0] === '-';
        $abs = $negative ? substr($this->value, 1) : $this->value;

        // Добавляем пол-единицы последнего знаке и отбрасываем остаток —
        // для неотрицательных чисел это и есть HALF_UP (равно — в большую сторону).
        $rounded = bcadd($abs, '0.' . str_repeat('0', $places) . '5', self::SCALE);
        $result = bcadd($rounded, '0', $places);

        if ($negative && bccomp($result, '0', $places) !== 0) {
            $result = '-' . $result;
        }

        return new self($result);
    }

    /**
     * Строка в точности с $places знаками после точки
     * (содержимое только дополняется нулями или обрывается, без округления).
     */
    public function format(int $places = 6): string
    {
        $sign = $this->value[0] === '-' ? '-' : '';
        $body = $sign === '-' ? substr($this->value, 1) : $this->value;
        $padded = bcadd($body, '0', $places);

        if ($sign === '-' && $padded === '0.' . str_repeat('0', $places)) {
            $sign = '';
        }

        return $sign . $padded;
    }

    private static function valueOf(Decimal|string|int $value): string
    {
        return $value instanceof self ? $value->value : self::of($value)->value;
    }
}
