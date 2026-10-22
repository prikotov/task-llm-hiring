<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeInterface;
use DomainException;

/**
 * Оф-пиковое окно провайдера: время суток UTC, [start, end),
 * окно может переходить через полночь (например 16:30–00:30).
 */
final class OffPeakWindow
{
    /**
     * @param string $multiplier десятичная строка, множитель стоимости события
     */
    private function __construct(
        private readonly int $startMinutes,
        private readonly int $endMinutes,
        private readonly string $multiplier,
    ) {
    }

    /**
     * @throws DomainException
     */
    public static function create(string $start, string $end, string $multiplier): self
    {
        if (preg_match('/^(0|[1-9]\d*)(\.\d+)?$/', $multiplier) !== 1) {
            throw new DomainException(sprintf('Оф-пик: "multiplier" должен быть неотрицательным десятичным числом, получено "%s".', $multiplier));
        }

        return new self(
            self::parseMinutes($start, 'window_start'),
            self::parseMinutes($end, 'window_end'),
            $multiplier,
        );
    }

    public static function fromParts(int $startMinutes, int $endMinutes, string $multiplier): self
    {
        return new self($startMinutes, $endMinutes, $multiplier);
    }

    /**
     * Попадает ли момент (UTC) в окно? start включительно, end исключительно.
     * Деградированное окно start == end трактуется как пустое.
     */
    public function contains(DateTimeInterface $timeUtc): bool
    {
        if ($this->startMinutes === $this->endMinutes) {
            return false;
        }

        $t = (int) $timeUtc->format('G') * 60 + (int) $timeUtc->format('i');

        if ($this->startMinutes < $this->endMinutes) {
            return $t >= $this->startMinutes && $t < $this->endMinutes;
        }

        // Окно пересекает полночь: [start, 24:00) ∪ [00:00, end)
        return $t >= $this->startMinutes || $t < $this->endMinutes;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }

    public function startMinutes(): int
    {
        return $this->startMinutes;
    }

    public function endMinutes(): int
    {
        return $this->endMinutes;
    }

    private static function parseMinutes(string $value, string $field): int
    {
        if (preg_match('/^([01]?\d|2[0-3]):([0-5]\d)$/', $value) !== 1) {
            throw new DomainException(sprintf('Оф-пик: "%s" — ожидается время суток "ЧЧ:ММ" (24-часовой формат), получено "%s".', $field, $value));
        }

        return (int) substr($value, 0, -3) * 60 + (int) substr($value, -2);
    }
}
