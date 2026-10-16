<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Оф-пиковое окно провайдера: [window_start, window_end), минуты от полуночи UTC,
 * окно может переходить через полночь.
 */
final class OffPeak
{
    /**
     * @param int|null $windowStart минуты от полуночи, включительно
     * @param int|null $windowEnd   минуты от полуночи, исключительно
     */
    public function __construct(
        private readonly ?int $windowStart,
        private readonly ?int $windowEnd,
        private readonly ?string $multiplier = null,
    ) {
    }

    public function isConfigured(): bool
    {
        return $this->windowStart !== null && $this->windowEnd !== null && $this->multiplier !== null;
    }

    public function minutesStart(): ?int
    {
        return $this->windowStart;
    }

    public function minutesEnd(): ?int
    {
        return $this->windowEnd;
    }

    public function multiplier(): ?string
    {
        return $this->multiplier;
    }

    /** Принадлежность момента (в минутах от полуночи UTC) окну. */
    public function contains(int $minutesOfDay): bool
    {
        if (!$this->isConfigured()) {
            return false;
        }

        $start = $this->windowStart;
        $end = $this->windowEnd;

        if ($start === $end) {
            return true; // вырожденное окно == полный день
        }

        if ($start < $end) {
            return $minutesOfDay >= $start && $minutesOfDay < $end;
        }

        // окно пересекает полночь
        return $minutesOfDay >= $start || $minutesOfDay < $end;
    }

    /**
     * @return int минуты от полуночи UTC
     */
    public static function minutesOf(\DateTimeImmutable $utc): int
    {
        return $utc->format('G') * 60 + (int) $utc->format('i');
    }

    /**
     * @param string $value формат "HH:MM" (24 часа)
     * @throws DomainException
     */
    public static function parseWindow(string $value): int
    {
        if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $value, $m)) {
            throw new DomainException(sprintf('Некорректное время окна "%s", ожидается HH:MM', $value));
        }

        return ((int) $m[1]) * 60 + (int) $m[2];
    }
}
