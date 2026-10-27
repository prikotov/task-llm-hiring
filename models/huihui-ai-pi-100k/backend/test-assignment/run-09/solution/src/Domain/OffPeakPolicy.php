<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Оф-пиковое окно провайдера (минуты суток UTC) и множитель стоимости.
 *
 * Окно [start, end) минут суток — start включительно, end исключительно;
 * может переходить через полночь (например, 16:30–00:30).
 * start == end — пустое окно (окружает что-либо).
 */
final class OffPeakPolicy
{
    private function __construct(
        private readonly int $startMinute,
        private readonly int $endMinute,
        private readonly string $multiplier,
    ) {
    }

    public static function fromMinutes(int $startMinute, int $endMinute, string $multiplier): self
    {
        return new self($startMinute, $endMinute, $multiplier);
    }

    public static function fromSpec(array $spec): self
    {
        $windowStart = self::stringField($spec, 'window_start');
        $windowEnd = self::stringField($spec, 'window_end');
        $multiplier = self::stringField($spec, 'multiplier');

        if (!preg_match('/^\d+$/', $multiplier) && !preg_match('/^\d+\.\d+$/', $multiplier)) {
            throw new InvalidArgumentException('off_peak.multiplier должен быть положительным десятичным числом');
        }

        return new self(
            self::toMinute($windowStart, 'window_start'),
            self::toMinute($windowEnd, 'window_end'),
            $multiplier,
        );
    }

    /** Окно [start, end): start включительно, end исключительно. */
    public function covers(DateTimeImmutable $ts): bool
    {
        $minute = (int) $ts->format('G') * 60 + (int) $ts->format('i');

        if ($this->startMinute <= $this->endMinute) {
            return $minute >= $this->startMinute && $minute < $this->endMinute;
        }

        // Окно переходит через полночь.
        return $minute >= $this->startMinute || $minute < $this->endMinute;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }
    public function startMinute(): int
    {
        return $this->startMinute;
    }

    public function endMinute(): int
    {
        return $this->endMinute;
    }

    public function windowStart(): string
    {
        return self::toHhmm($this->startMinute);
    }

    public function windowEnd(): string
    {
        return self::toHhmm($this->endMinute);
    }

    private static function stringField(array $spec, string $field): string
    {
        $value = $spec[$field] ?? null;
        if (!is_string($value) || $value === '') {
            throw new InvalidArgumentException("off_peak.$field — обязательное непустое строковое поле");
        }

        return $value;
    }

    private static function toMinute(string $hhmm, string $field): int
    {
        if (!preg_match('/^([01]\d|2[0-4]):([0-5]\d)$/', $hhmm, $m)) {
            throw new InvalidArgumentException("off_peak.$field: ожидается время суток HH:MM (00:00–24:00), получено: $hhmm");
        }

        return ((int) $m[1]) * 60 + (int) $m[2];
    }

    private static function toHhmm(int $min): string
    {
        return sprintf('%02d:%02d', intdiv($min, 60), $min % 60);
    }
}
