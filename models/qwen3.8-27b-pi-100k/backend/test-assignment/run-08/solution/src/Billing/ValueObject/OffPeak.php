<?php

declare(strict_types=1);

namespace App\Billing\ValueObject;

use App\Billing\Import\ImportException;
use DateTimeImmutable;

/**
 * Оф-пиковое окно провайдера (UTC): [window_start, window_end),
 * окно может переходить через полночь (16:30–00:30).
 */
final class OffPeak
{
    /**
     * @param int $windowStartMinutes минуты от полуночи UTC, включительно
     * @param int $windowEndMinutes   минуты от полуночи UTC, исключительно
     */
    public function __construct(
        public readonly Decimal $multiplier,
        public readonly int $windowStartMinutes,
        public readonly int $windowEndMinutes,
    ) {
    }

    public static function fromInput(string $windowStart, string $windowEnd, string $multiplier): self
    {
        return new self(
            Decimal::of($multiplier),
            self::parseTimeOfDay($windowStart, 'off_peak.window_start'),
            self::parseTimeOfDay($windowEnd, 'off_peak.window_end'),
        );
    }

    public function isOffPeak(DateTimeImmutable $tsUtc): bool
    {
        $t = ((int) $tsUtc->format('H')) * 60 + ((int) $tsUtc->format('i'));
        $start = $this->windowStartMinutes;
        $end = $this->windowEndMinutes;
        // start == end интерпретируем как пустое окно (невозможно выразить «24 часа»).
        return $start < $end
            ? ($t >= $start && $t < $end)
            : ($t >= $start || $t < $end); // окно через полночь
    }

    public static function parseTimeOfDay(string $value, string $field): int
    {
        if (preg_match('/^([01]?\d|2[0-3]):([0-5]\d)$/', $value, $m) !== 1) {
            throw new ImportException(sprintf('Поле "%s": ожидалось "HH:MM" (UTC), получено "%s".', $field, $value));
        }

        return ((int) $m[1]) * 60 + ((int) $m[2]);
    }
}
