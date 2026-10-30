<?php

declare(strict_types=1);

namespace App\Domain;

use App\Application\Exception\BillingException;
use DateImmutable;

/**
 * Оф-пиковое окно провайдера (UTC, по времени суток).
 *
 * Окно [start, end); поддерживает переход через полночь (например 16:30–00:30).
 * Если start == end — окно пустое (скидка нигде не действует).
 */
final class OffPeakWindow
{
    /**
     * @param int $startMin минуты от начала суток UTC для window_start (включительно)
     * @param int $endMin   минуты от начала суток UTC для window_end (исключительно)
     */
    private function __construct(
        private readonly int $startMin,
        private readonly int $endMin,
        private readonly Money $multiplier
    ) {
        if ($startMin < 0 || $startMin >= 1440) {
            throw BillingException::invalidValue(sprintf('window_start вне суток: %d', $startMin));
        }
        if ($endMin < 0 || $endMin >= 1440) {
            throw BillingException::invalidValue(sprintf('window_end вне суток: %d', $endMin));
        }
        if ($multiplier->isNegative()) {
            throw BillingException::invalidValue('multiplier должен быть неотрицательным');
        }
    }

    public static function fromParts(int $startMin, int $endMin, Money $multiplier): self
    {
        return new self($startMin, $endMin, $multiplier);
    }

    public function contains(\DateTimeInterface $utc): bool
    {
        if ($this->startMin === $this->endMin) {
            return false;
        }

        // минуты от начала суток UTC (секунды внутри минуты — внутри окна, игнорируем)
        $m = ((int) $utc->format('G')) * 60 + (int) $utc->format('i');

        if ($this->startMin < $this->endMin) {
            return $m >= $this->startMin && $m < $this->endMin;
        }

        // окно через полночь: [start, 24:00) ∪ [00:00, end)
        return $m >= $this->startMin || $m < $this->endMin;
    }

    public function isOffPeak(DateImmutable $utc): bool
    {
        return $this->contains($utc);
    }

    public function multiplier(): Money
    {
        return $this->multiplier;
    }

    public function startMinutes(): int
    {
        return $this->startMin;
    }

    public function endMinutes(): int
    {
        return $this->endMin;
    }
}
