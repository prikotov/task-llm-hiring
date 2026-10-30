<?php

declare(strict_types=1);

namespace App\Domain;

use App\Exception\ImportException;

/**
 * Оф-пиковое окно скидки провайдера (время суток UTC).
 *
 * Окно полуинтервал [window_start, window_end), минута — атом;
 * может переходить через полночь (16:30–00:30).
 */
final class OffPeak
{
    private function __construct(
        private readonly int $startMinutes,
        private readonly int $endMinutes,
        private readonly string $multiplier,
    ) {}

    public static function create(string $windowStart, string $windowEnd, string $multiplier): self
    {
        $start = self::toMinutes($windowStart, 'window_start', false);
        $end = self::toMinutes($windowEnd, 'window_end', true);

        if (self::decimal($multiplier) === '') {
            throw new ImportException(sprintf('off_peak.multiplier: некорректное десятичное значение "%s"', $multiplier));
        }

        return new self($start, $end, $multiplier);
    }

    /** Событие попадает в окно? (сравнение по минутам UTC). */
    public function appliesAt(\DateTimeInterface $utcTs): bool
    {
        $current = ((int) $utcTs->format('H')) * 60 + (int) $utcTs->format('i');

        if ($this->startMinutes <= $this->endMinutes) {
            return $current >= $this->startMinutes && $current < $this->endMinutes;
        }

        // окно через полночь
        return $current >= $this->startMinutes || $current < $this->endMinutes;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }

    public function startText(): string
    {
        return sprintf('%02d:%02d', intdiv($this->startMinutes, 60), $this->startMinutes % 60);
    }

    public function endText(): string
    {
        return sprintf('%02d:%02d', intdiv($this->endMinutes, 60), $this->endMinutes % 60);
    }

    private static function toMinutes(string $hhmm, string $field, bool $isEnd = false): int
    {
        $pattern = $isEnd ? '/^(?:([01]\d|2[0-3]):([0-5]\d)|(24:00))$/' : '/^([01]\d|2[0-3]):([0-5]\d)$/';
        if (!preg_match($pattern, $hhmm, $m)) {
            throw new ImportException(sprintf(
                'off_peak.%s: ожидается "ЧЧ:ММ" (UTC), получено "%s"',
                $field,
                $hhmm,
            ));
        }

        if (($m[3] ?? '') === '24:00') {
            return 24 * 60;
        }

        return ((int) $m[1]) * 60 + (int) $m[2];
    }

    private static function decimal(string $value): string
    {
        return preg_match('/^\d+(\.\d{1,6})?$/', trim($value)) ? $value : '';
    }
}
