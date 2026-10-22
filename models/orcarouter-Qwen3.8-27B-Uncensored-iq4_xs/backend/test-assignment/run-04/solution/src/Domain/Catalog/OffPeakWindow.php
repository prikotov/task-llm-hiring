<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Оф-пиковое окно провайдера по времени суток UTC: [window_start, window_end).
 * Окно может переходить через полночь (16:30–00:30).
 *
 * Допущение: окно с window_start == window_end считается пустым (скидки нет) —
 * трактовка как «24 часа» выглядит противоречивее; зафиксировано в PR_DESCRIPTION.md.
 */
final class OffPeakWindow
{
    private readonly int $startMinutes;
    private readonly int $endMinutes;

    public function __construct(
        string $windowStart,
        string $windowEnd,
        public readonly string $multiplier,
    ) {
        if (!self::isHhMm($windowStart) || !self::isHhMm($windowEnd)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Off-peak window bounds must be "HH:MM", got "%s"–"%s".',
                    $windowStart,
                    $windowEnd,
                ),
            );
        }
        if (!preg_match('/^\d+(\.\d+)?$/', $multiplier) || bccomp($multiplier, '0', 10) <= 0) {
            throw new InvalidArgumentException(
                sprintf('Off-peak multiplier must be a positive decimal, got "%s".', $multiplier),
            );
        }

        $this->startMinutes = self::toMinutes($windowStart);
        $this->endMinutes = self::toMinutes($windowEnd);
    }

    /**
     * Попадает ли момент (UTC) в окно скидки.
     */
    public function appliesAtUtc(DateTimeImmutable $time): bool
    {
        if ($this->startMinutes === $this->endMinutes) {
            return false;
        }

        $minutes = ((int) $time->format('H')) * 60 + (int) $time->format('i');

        if ($this->startMinutes < $this->endMinutes) {
            return $minutes >= $this->startMinutes && $minutes < $this->endMinutes;
        }

        // Окно через полночь: [start, 24:00) ∪ [00:00, end)
        return $minutes >= $this->startMinutes || $minutes < $this->endMinutes;
    }

    private static function isHhMm(string $value): bool
    {
        return preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value) === 1;
    }

    private static function toMinutes(string $hhMm): int
    {
        [$h, $m] = explode(':', $hhMm);

        return ((int) $h) * 60 + (int) $m;
    }
}
