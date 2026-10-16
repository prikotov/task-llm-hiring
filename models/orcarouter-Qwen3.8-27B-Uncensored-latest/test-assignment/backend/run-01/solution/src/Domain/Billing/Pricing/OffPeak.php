<?php

declare(strict_types=1);

namespace App\Domain\Billing\Pricing;

/**
 * Оф-пиковая скидка провайдера. Окно времени суток UTC:
 * начало включительно, конец исключительно, может переходить через полночь
 * (например 16:30–00:30).
 */
final class OffPeak
{
    public function __construct(
        public readonly string $windowStart, // "16:30"
        public readonly string $windowEnd,   // "00:30"
        public readonly string $multiplier,  // "0.5"
    ) {
    }

    public static function fromArray(array $data): self
    {
        foreach (['window_start', 'window_end', 'multiplier'] as $field) {
            if (!isset($data[$field]) || $data[$field] === null || $data[$field] === '') {
                throw new \InvalidArgumentException(
                    sprintf('off_peak.%s: значение обязательно.', $field)
                );
            }
        }

        return new self(
            self::normalizeTime($data['window_start'], 'off_peak.window_start'),
            self::normalizeTime($data['window_end'], 'off_peak.window_end'),
            self::normalizeDecimal((string) $data['multiplier'], 'off_peak.multiplier')
        );
    }

    public function isInWindow(\DateTimeImmutable $utcMoment): bool
    {
        $t = self::toMinutes($utcMoment->format('H:i'));
        $start = self::toMinutes($this->windowStart);
        $end = self::toMinutes($this->windowEnd);

        if ($start === $end) {
            // Degenerate window — treat as "always off-peak" only if multiplier applied.
            return true;
        }

        if ($start < $end) {
            return $t >= $start && $t < $end;
        }

        // Окно пересекает полночь: [16:30, 24:00) ∪ [00:00, 00:30)
        return $t >= $start || $t < $end;
    }

    private static function toMinutes(string $hhmm): int
    {
        $parts = explode(':', $hhmm, 2);
        return ((int) $parts[0]) * 60 + ((int) $parts[1]);
    }

    private static function normalizeTime(string $value, string $context): string
    {
        if (!preg_match('/^([01]?\d|2[0-3]):([0-5]\d)$/', $value, $m)) {
            throw new \InvalidArgumentException(
                sprintf('%s: ожидалось время в формате "HH:MM", получено "%s".', $context, $value)
            );
        }
        return sprintf('%02d:%02d', (int) $m[1], (int) $m[2]);
    }

    private static function normalizeDecimal(string $value, string $context): string
    {
        if (!preg_match('/^-?\d+(\.\d+)?$/', $value)) {
            throw new \InvalidArgumentException(
                sprintf('%s: ожидалось десятичное число, получено "%s".', $context, $value)
            );
        }
        return $value;
    }
}
