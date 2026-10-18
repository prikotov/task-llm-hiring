<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use DateTimeInterface;
use InvalidArgumentException;

/**
 * Оф-пиковое окно провайдера по времени суток UTC.
 *
 * Окно полуинтервал [start, end), минуты от полуночи UTC;
 * может переходить через полночь (например, 16:30–00:30).
 * Пустое окно (start == end) ничего не содержит.
 */
final class OffPeakWindow
{
    public function __construct(
        private readonly int $startMinutes,
        private readonly int $endMinutes,
        private readonly string $multiplier,
    ) {
        if ($startMinutes < 0 || $startMinutes > 1439) {
            throw new InvalidArgumentException('Начало окна должно быть в пределах суток (00:00–23:59).');
        }
        if ($endMinutes < 0 || $endMinutes > 1439) {
            throw new InvalidArgumentException('Конец окна должен быть в пределах суток (00:00–23:59).');
        }
    }

    public function startMinutes(): int
    {
        return $this->startMinutes;
    }

    public function endMinutes(): int
    {
        return $this->endMinutes;
    }

    /** Множитель стоимости события, попавшего в окно (десятичная строка, > 0). */
    public function multiplier(): string
    {
        return $this->multiplier;
    }

    public function isWithin(DateTimeInterface $utcTime): bool
    {
        if ($this->startMinutes === $this->endMinutes) {
            return false;
        }

        $minutes = (int) $utcTime->format('G') * 60 + (int) $utcTime->format('i');

        if ($this->startMinutes < $this->endMinutes) {
            return $minutes >= $this->startMinutes && $minutes < $this->endMinutes;
        }

        // Окно пересекает полночь: например, 16:30 ≤ t или t < 00:30.
        return $minutes >= $this->startMinutes || $minutes < $this->endMinutes;
    }
}
