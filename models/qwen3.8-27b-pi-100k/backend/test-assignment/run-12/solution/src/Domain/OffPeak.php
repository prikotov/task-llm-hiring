<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Оф-пиковое окно.
 *
 * Включительно по start (HH:MM), исключительно по end.
 * Если start <= end — обычное окно. Если start > end — переход через полночь
 * (например 16:30–00:30).
 */
final class OffPeak
{
    public function __construct(
        public readonly int $startMinutes, // минуты с начала суток [0..1440)
        public readonly int $endMinutes,
        public readonly string $multiplier, // bcmath строка, напр. "0.5"
    ) {
    }

    public static function fromConfig(array $config): self
    {
        $start = self::parseHM($config['window_start'] ?? null, 'window_start');
        $end = self::parseHM($config['window_end'] ?? null, 'window_end');
        $multiplier = $config['multiplier'] ?? null;
        if (!is_string($multiplier) || !preg_match('/^(0|[1-9]\d*)(\.\d+)?$/', $multiplier)) {
            throw new \InvalidArgumentException('off_peak.multiplier: invalid decimal string');
        }
        return new self(self::minOfDay($start), self::minOfDay($end), $multiplier);
    }

    public function covers(\DateTimeImmutable $ts): bool
    {
        $minuteOfDay = (int) $ts->format('H') * 60 + (int) $ts->format('i');
        if ($this->startMinutes <= $this->endMinutes) {
            return $minuteOfDay >= $this->startMinutes && $minuteOfDay < $this->endMinutes;
        }
        return $minuteOfDay >= $this->startMinutes || $minuteOfDay < $this->endMinutes;
    }

    /** @return array{0:string, 1:string} "HH","MM" */
    private static function parseHM(mixed $value, string $label): array
    {
        if (!is_string($value) || !preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $value, $m)) {
            throw new \InvalidArgumentException(sprintf('off_peak.%s: expect "HH:MM" (00:00..23:59), got "%s"', $label, is_string($value) ? $value : get_debug_type($value)));
        }
        return [$m[1], $m[2]];
    }

    private static function minOfDay(array $hm): int
    {
        return (int) $hm[0] * 60 + (int) $hm[1];
    }
}
