<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

/**
 * Невариантная десятичная величина денег (bcmath, внутренний масштаб 24).
 *
 * Инварианты:
 *  - хранится как строка вида "123.456" (десятичная, без экспоненции);
 *  - все операции выполняются bcmath — тип float в деньгах не используется;
 *  - итоговое представление стоимости события — 6 знаков после точки (roundTo6).
 */
final class Money
{
    public const INTERNAL_SCALE = 24;
    public const FINAL_SCALE = 6;

    private function __construct(
        private readonly string $value,
    ) {
    }

    public static function of(string $value): self
    {
        if (!preg_match('/^-?\d+(\.\d+)?$/', $value)) {
            throw new \InvalidArgumentException(sprintf('Некорректная десятичная строка: %s', $value));
        }

        return new self($value);
    }

    public static function zero(): self
    {
        return new self('0');
    }

    /** Точное значение как строка. */
    public function value(): string
    {
        return $this->value;
    }

    public function isNegative(): bool
    {
        return str_starts_with($this->value, '-');
    }

    /** Сложение без округления (обе слагаемые — точные десятичные). */
    public function add(self $other): self
    {
        return new self(bcadd($this->value, $other->value, self::INTERNAL_SCALE));
    }

    /** Сложение строковой десятичной (rates хранятся в строках). */
    public function addDecimal(string $other): self
    {
        return new self(bcadd($this->value, $other, self::INTERNAL_SCALE));
    }

    /** Умножение на десятичный множитель (off-peak). */
    public function multipliedBy(string $factor): self
    {
        return new self(bcmul($this->value, $factor, self::INTERNAL_SCALE));
    }

    /**
     * Деление на целое (делитель 1 000 000 для per-1m-схем) с запасом масштаба.
     * Делитель — степень 10, результат на масштабе >= 6 — точный.
     */
    public function dividedBy(int $divisor): self
    {
        return new self(bcdiv($this->value, (string) $divisor, self::INTERNAL_SCALE));
    }

    /**
     * Округление до 6 знаков после точки, HALF_UP (2.5 → 3; для неотрицательных —
     * «+ половина шага и отброс хвоста»). Результат — ровно 6 знаков.
     */
    public function roundTo6(): self
    {
        if ($this->isNegative()) {
            throw new \LogicException('Округление отрицательной стоимости события не ожидается');
        }

        $shifted = bcadd($this->value, '0.0000005', self::INTERNAL_SCALE);

        return new self(bcdiv($shifted, '1', self::FINAL_SCALE));
    }

    /**
     * Форматировать в отчёт: ровно 6 знаков после точки.
     * Допустимо только для величин, которые не грубее 6 знаков (суммы
     * округлённых стоимостей событий — такие).
     */
    public function format6(): string
    {
        return bcadd($this->value, '0', self::FINAL_SCALE);
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
