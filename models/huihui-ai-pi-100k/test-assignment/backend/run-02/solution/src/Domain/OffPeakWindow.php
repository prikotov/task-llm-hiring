<?php

declare(strict_types=1);

namespace App\Domain;

use ValueError;

/**
 * Оф-пиковое окно провайдера (время суток UTC) и множитель стоимости.
 *
 * Окно [start, end): start включительно, end исключительно. Может
 * переходить через полночь (например, 16:30–00:30). Если start == end,
 * окно считается пустым (скидки нет) — дедлисное определение.
 *
 * Множитель хранится как десятичная строка: тип float для денег не используется.
 */
final class OffPeakWindow
{
    public function __construct(
        private readonly int $startSeconds,
        private readonly int $endSeconds,
        private readonly string $multiplier,
    ) {
        if ($startSeconds < 0 || $startSeconds >= 86_400 || $endSeconds < 0 || $endSeconds >= 86_400) {
            throw new ValueError('OffPeakWindow: секунды суток должны быть в диапазоне 0..86399');
        }
    }

    /**
     * @param string $windowStart  время в формате ЧЧ:ММ (UTC)
     * @param string $windowEnd    время в формате ЧЧ:ММ (UTC)
     * @param string $multiplier   десятичная строка, например «0.5»
     *
     * @throws ValueError при некорректных значениях
     */
    public static function parse(string $windowStart, string $windowEnd, string $multiplier): self
    {
        if (!preg_match('/^\d+(\.\d+)?$/', $multiplier)) {
            throw new ValueError('OffPeakWindow: множитель должен быть неотрицательным десятичным числом, получено: ' . $multiplier);
        }

        return new self(
            self::parseTimeOfDay($windowStart, 'off_peak.window_start'),
            self::parseTimeOfDay($windowEnd, 'off_peak.window_end'),
            $multiplier,
        );
    }

    /** Парсит «ЧЧ:ММ» (UTC) в секунды с начала суток. */
    public static function parseTimeOfDay(string $value, string $field): int
    {
        if (preg_match('/^(\d{1,2}):(\d{2})$/', $value, $parts) !== 1) {
            throw new ValueError($field . ': ожидалось время в формате ЧЧ:ММ, получено: ' . $value);
        }

        $hours = (int) $parts[1];
        $minutes = (int) $parts[2];
        if ($hours > 23 || $minutes > 59) {
            throw new ValueError($field . ': некорректное время суток: ' . $value);
        }

        return $hours * 3600 + $minutes * 60;
    }

    public function startSeconds(): int
    {
        return $this->startSeconds;
    }

    public function endSeconds(): int
    {
        return $this->endSeconds;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }

    /**
     * Попадает ли момент (UTC) в окно?
     *
     * start включительно, end исключительно; окно, переходящее через
     * полночь, расшифровывается как [start; 24:00) ∪ [00:00; end).
     */
    public function covers(\DateTimeInterface $utcTimestamp): bool
    {
        $timeOfDay = (int) $utcTimestamp->format('H') * 3600
            + (int) $utcTimestamp->format('i') * 60
            + (int) $utcTimestamp->format('s');

        if ($this->startSeconds === $this->endSeconds) {
            return false;
        }

        if ($this->startSeconds < $this->endSeconds) {
            return $timeOfDay >= $this->startSeconds && $timeOfDay < $this->endSeconds;
        }

        return $timeOfDay >= $this->startSeconds || $timeOfDay < $this->endSeconds;
    }
}
