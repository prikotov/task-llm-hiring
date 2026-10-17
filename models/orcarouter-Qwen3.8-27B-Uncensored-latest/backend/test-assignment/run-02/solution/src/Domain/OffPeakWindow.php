<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use function bcmul;

/**
 * Оф-пиковое окно провайдера: [window_start, window_end) по UTC,
 * может переходить через полночь (16:30–00:30).
 * multiplier — точная десятичная строка, на которую умножается
 * стоимость события, попавшего в окно.
 */
final class OffPeakWindow
{
    private const SCALE = 20;

    public function __construct(
        public readonly int $startSecondOfDay,
        public readonly int $endSecondOfDay,
        public readonly string $multiplier,
    ) {
    }

    public static function fromRaw(string $windowStart, string $windowEnd, string $multiplier): self
    {
        return new self(
            self::parseTime($windowStart, 'off_peak.window_start'),
            self::parseTime($windowEnd, 'off_peak.window_end'),
            $multiplier,
        );
    }

    /** JSON-представление для колонки providers.off_peak. */
    public function encode(): array
    {
        return [
            'start' => $this->startSecondOfDay,
            'end' => $this->endSecondOfDay,
            'multiplier' => $this->multiplier,
        ];
    }

    /** @param array{start: int, end: int, multiplier: string} $raw */
    public static function decode(array $raw): self
    {
        return new self((int) $raw['start'], (int) $raw['end'], (string) $raw['multiplier']);
    }

    /** Проходит ли момент времени внутрь окна (сравнение по секундам дня UTC). */
    public function inWindow(DateTimeImmutable $tsUtc): bool
    {
        $sec = ((int) $tsUtc->format('H')) * 3600
            + ((int) $tsUtc->format('i')) * 60
            + ((int) $tsUtc->format('s'));

        if ($this->startSecondOfDay < $this->endSecondOfDay) {
            return $sec >= $this->startSecondOfDay && $sec < $this->endSecondOfDay;
        }

        // Окно через полночь: [start, 24:00) + [00:00, end)
        return $sec >= $this->startSecondOfDay || $sec < $this->endSecondOfDay;
    }

    /** Возвращает стоимость события с учётом окна (без изменений, если событие вне окна). */
    public function apply(string $cost, DateTimeImmutable $tsUtc): string
    {
        if (!$this->inWindow($tsUtc) || $this->multiplier === '1') {
            return $cost;
        }

        return bcmul($cost, $this->multiplier, self::SCALE);
    }

    private static function parseTime(string $value, string $field): int
    {
        if (!preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $value, $m)) {
            throw new \InvalidArgumentException(sprintf('%s: expected "HH:MM", got "%s"', $field, $value));
        }

        return ((int) $m[1]) * 3600 + ((int) $m[2]) * 60;
    }
}
