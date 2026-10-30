<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Оф-пиковое окно провайдера: [window_start, window_end) по часам UTC.
 * Окно может переходить через полночь (напр. 16:30–00:30).
 */
final class OffPeakWindow
{
    /** Число минут от 00:00. */
    private int $startMin;
    private int $endMin;
    /** Множитель стоимости. */
    private string $multiplier;

    private function __construct(string $windowStart, string $windowEnd, string $multiplier)
    {
        $this->startMin = self::toMinutes($windowStart);
        $this->endMin = self::toMinutes($windowEnd);
        $this->multiplier = $multiplier;
    }

    public static function fromData(array $data): self
    {
        return new self(
            $data['window_start'],
            $data['window_end'],
            $data['multiplier']
        );
    }

    /**
     * Проверка: попадает ли время в окно?
     *
     * Окно [start, end):
     * - Если endMin > startMin: обычный интервал (e.g. 00:30–16:30)
     * - Если endMin <= startMin: окно через полночь (e.g. 16:30–00:30)
     */
    public function isWithin(int $timeOfDayMinutes): bool
    {
        if ($this->endMin > $this->startMin) {
            // Обычное окно: start ≤ t < end
            return $this->startMin <= $timeOfDayMinutes && $timeOfDayMinutes < $this->endMin;
        }
        // Окно через полночь: t >= start OR t < end
        return $this->startMin <= $timeOfDayMinutes || $timeOfDayMinutes < $this->endMin;
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }

    private static function toMinutes(string $hms): int
    {
        // Формат "HH:MM"
        [$h, $m] = array_map('intval', explode(':', $hms));
        return $h * 60 + $m;
    }
}
