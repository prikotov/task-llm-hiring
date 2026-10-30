<?php

declare(strict_types=1);

namespace App\Billing\Domain\Catalog;

use App\Billing\Domain\Shared\Decimal;
use DateTimeImmutable;
use InvalidArgumentException;

/**
 * Оф-пиковое окно провайдера по времени суток UTC: [start, end).
 * Может переходить через полночь (start > end). start == end — окна нет.
 */
final class OffPeakWindow
{
    private readonly int $startMinute;
    private readonly int $endMinute;

    public function __construct(
        public readonly string $start,
        public readonly string $end,
        public readonly string $multiplier,
    ) {
        $this->startMinute = self::toMinutes($start);
        $this->endMinute = self::toMinutes($end);

        if (preg_match('/^\d+(\.\d{1,6})?$/', $multiplier) !== 1) {
            throw new InvalidArgumentException(sprintf('Некорректный множитель оф-пика: "%s"', $multiplier));
        }
    }

    public function contains(DateTimeImmutable $ts): bool
    {
        $utc = $ts->setTimezone(new \DateTimeZone('UTC'));
        $minute = (int) $utc->format('H') * 60 + (int) $utc->format('i');

        if ($this->startMinute === $this->endMinute) {
            return false;
        }

        if ($this->startMinute < $this->endMinute) {
            return $minute >= $this->startMinute && $minute < $this->endMinute;
        }

        // окно через полночь: [start, 24:00) ∪ [00:00, end)
        return $minute >= $this->startMinute || $minute < $this->endMinute;
    }

    public function multiplierValue(): string
    {
        return Decimal::formatMoney($this->multiplier);
    }

    private static function toMinutes(string $hhmm): int
    {
        if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $hhmm, $m) !== 1) {
            throw new InvalidArgumentException(sprintf('Некорректное время окна оф-пика: "%s"', $hhmm));
        }

        return (int) $m[1] * 60 + (int) $m[2];
    }
}
