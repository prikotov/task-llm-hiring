<?php

declare(strict_types=1);

namespace App\Domain;

use ValueError;

/**
 * Оф-пиковое окно провайдера: [window_start, window_end) по минутам суток UTC.
 *
 * Окно может переходить через полночь (например, 16:30–00:30).
 * При start == end длительность окна считается нулевой — скидка не применяется
 * (допущение зафиксировано в PR_DESCRIPTION.md).
 */
final class OffPeakWindow
{
    public function __construct(
        private readonly int $startMinute,
        private readonly int $endMinute,
        private readonly string $multiplier,
    ) {
        if (!$this->multiplierIsValid($multiplier)) {
            throw new ValueError(sprintf('Оф-пиковый множитель должен быть неотрицательным десятичным числом, получен «%s».', $multiplier));
        }
    }

    public static function create(int $startMinute, int $endMinute, string $multiplier): self
    {
        if ($startMinute < 0 || $startMinute > 1439 || $endMinute < 0 || $endMinute > 1439) {
            throw new ValueError('Оф-пиковое окно должно укладываться в сутки (00:00–23:59 UTC).');
        }

        return new self($startMinute, $endMinute, $multiplier);
    }

    public function containsMinute(int $minuteOfDayUtc): bool
    {
        if ($this->startMinute === $this->endMinute) {
            return false;
        }

        if ($this->startMinute < $this->endMinute) {
            return $minuteOfDayUtc >= $this->startMinute && $minuteOfDayUtc < $this->endMinute;
        }

        return $minuteOfDayUtc >= $this->startMinute || $minuteOfDayUtc < $this->endMinute;
    }

    public function startMinute(): int
    {
        return $this->startMinute;
    }

    public function endMinute(): int
    {
        return $this->endMinute;
    }

    /** Множитель стоимости (десятичная строка). */
    public function multiplier(): string
    {
        return $this->multiplier;
    }

    private function multiplierIsValid(string $multiplier): bool
    {
        return preg_match('/^\d{1,20}(\.\d{1,12})?$/', $multiplier) === 1;
    }
}
