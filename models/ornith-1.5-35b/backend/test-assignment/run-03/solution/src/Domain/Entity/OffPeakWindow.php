<?php

declare(strict_types=1);

namespace App\Domain\Entity;

/**
 * Значимо-независимый тип: представление оф-пикового окна провайдера.
 */
final class OffPeakWindow
{
    public function __construct(
        private readonly ?string $start,
        private readonly ?string $end,
        private readonly ?string $multiplier,
    ) {
    }

    public function isActive(): bool
    {
        return $this->multiplier !== null;
    }

    /**
     * Попадает ли заданное количество секунд от полуночи (UTC) в окно.
     * Начало включительно, исключение — exclusively end.
     * Окно может переходить через полночь.
     */
    public function contains(int $secondsFromMidnight): bool
    {
        if (!$this->isActive()) {
            return false;
        }

        $start = $this->toSeconds($this->start);
        $end = $this->toSeconds($this->end);

        if ($start === null || $end === null) {
            return false;
        }

        if ($start < $end) {
            return $secondsFromMidnight >= $start && $secondsFromMidnight < $end;
        }

        if ($start > $end) {
            // окно через полночь
            return $secondsFromMidnight >= $start || $secondsFromMidnight < $end;
        }

        // start === end: вырожденное окно — скидки нет
        return false;
    }

    public function multiplier(): ?string
    {
        return $this->multiplier;
    }

    private function toSeconds(string $hhmm): ?int
    {
        if (!preg_match('/^(\d{2}):(\d{2})$/', $hhmm, $m)) {
            return null;
        }
        $h = (int) $m[1];
        $mi = (int) $m[2];
        if ($h > 23 || $mi > 59) {
            return null;
        }

        return $h * 3600 + $mi * 60;
    }
}
