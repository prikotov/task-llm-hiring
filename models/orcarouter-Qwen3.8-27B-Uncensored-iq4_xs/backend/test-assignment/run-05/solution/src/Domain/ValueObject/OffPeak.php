<?php

declare(strict_types=1);

namespace App\Domain\ValueObject;

/**
 * Оф-пиковое окно провайдера по времени суток UTC:
 * [windowStart, windowEnd), окно может переходить через полночь.
 */
final class OffPeak
{
    public function __construct(
        private readonly string $windowStart,
        private readonly string $windowEnd,
        private readonly string $multiplier,
    ) {
        if (!preg_match('/^\d{2}:\d{2}$/', $this->windowStart) || !preg_match('/^\d{2}:\d{2}$/', $this->windowEnd)) {
            throw new \DomainException('off_peak окно: ожидается "HH:MM"');
        }
        if (!preg_match('/^\d+(\.\d{1,6})?$/', $this->multiplier)) {
            throw new \DomainException('off_peak.multiplier: ожидается десятичная строка (>= 0, до 6 знаков)');
        }
    }

    public static function create(?array $source): ?self
    {
        if ($source === null) {
            return null;
        }

        $windowStart = $source['window_start'] ?? null;
        $windowEnd = $source['window_end'] ?? null;
        $multiplier = $source['multiplier'] ?? null;

        if (!is_string($windowStart) || !preg_match('/^\d{2}:\d{2}$/', $windowStart)) {
            throw new \DomainException('off_peak.window_start: ожидается "HH:MM"');
        }
        if (!is_string($windowEnd) || !preg_match('/^\d{2}:\d{2}$/', $windowEnd)) {
            throw new \DomainException('off_peak.window_end: ожидается "HH:MM"');
        }
        if (!is_string($multiplier) || !preg_match('/^\d+(\.\d{1,6})?$/', $multiplier)) {
            throw new \DomainException('off_peak.multiplier: ожидается десятичная строка (>= 0, до 6 знаков)');
        }

        return new self($windowStart, $windowEnd, $multiplier);
    }

    /** Время ts — в UTC; проверка попадания в [windowStart, windowEnd). Degenerate (start == end) — пустое окно. */
    public function covers(\DateTimeImmutable $tsUtc): bool
    {
        $secondOfDay = ($tsUtc->format('H')) * 3600 + ($tsUtc->format('i')) * 60 + ($tsUtc->format('s'));
        $start = $this->toSecondsOfDay($this->windowStart);
        $end = $this->toSecondsOfDay($this->windowEnd);

        if ($start === $end) {
            return false;
        }
        if ($start < $end) {
            return $secondOfDay >= $start && $secondOfDay < $end;
        }

        // Окно пересекает полночь, например 16:30–00:30.
        return $secondOfDay >= $start || $secondOfDay < $end;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }

    public function windowStart(): string
    {
        return $this->windowStart;
    }

    public function windowEnd(): string
    {
        return $this->windowEnd;
    }

    private function toSecondsOfDay(string $hhmm): int
    {
        [$h, $m] = explode(':', $hhmm);

        return ((int) $h) * 3600 + ((int) $m) * 60;
    }
}
