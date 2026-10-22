<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Money\Decimal;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Оф-пиковое окно провайдера: интервал времени суток UTC
 * [windowStart, windowEnd), в котором стоимость события умножается
 * на multiplier. Окно может переходить через полночь (16:30–00:30).
 */
final readonly class OffPeakWindow
{
    public const TIME_FORMAT = 'H:i';

    private function __construct(
        public int $startMinute,
        public int $endMinute,
        public Decimal $multiplier,
    ) {
    }

    public static function fromStrings(string $windowStart, string $windowEnd, string $multiplier): self
    {
        $multiplierValue = Decimal::fromString($multiplier);
        if ($multiplierValue->compare(Decimal::zero()) < 0) {
            throw new InvalidArgumentException('Множитель оф-пика не может быть отрицательным.');
        }

        return new self(
            self::parseMinute($windowStart),
            self::parseMinute($windowEnd),
            $multiplierValue,
        );
    }

    public function windowStartLabel(): string
    {
        return self::formatMinute($this->startMinute);
    }

    public function windowEndLabel(): string
    {
        return self::formatMinute($this->endMinute);
    }

    /**
     * Входит ли момент (UTC) в окно. Границы: начало включительно,
     * конец исключительно. Пустое окно (start == end) не срабатывает никогда.
     */
    public function contains(DateTimeImmutable $moment): bool
    {
        $minute = self::minuteOfDay($moment);

        if ($this->startMinute === $this->endMinute) {
            return false;
        }

        if ($this->startMinute < $this->endMinute) {
            return $minute >= $this->startMinute && $minute < $this->endMinute;
        }

        // Переход через полночь: [start, 24:00) ∪ [00:00, end)
        return $minute >= $this->startMinute || $minute < $this->endMinute;
    }

    public static function minuteOfDay(DateTimeImmutable $moment): int
    {
        $utc = $moment->setTimezone(new DateTimeZone('UTC'));

        return (int) $utc->format('G') * 60 + (int) $utc->format('i');
    }

    private static function parseMinute(string $value): int
    {
        if (1 !== preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $value, $m)) {
            throw new InvalidArgumentException(sprintf('Некорректное время окна: "%s".', $value));
        }

        return (int) $m[1] * 60 + (int) $m[2];
    }

    private static function formatMinute(int $minute): string
    {
        return sprintf('%02d:%02d', intdiv($minute, 60), $minute % 60);
    }
}
