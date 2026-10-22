<?php

declare(strict_types=1);

namespace App\Billing\Domain\Model;

use App\Billing\Domain\Money\Money;

/**
 * Время суток UTC с точностью до секунды. Принимает "16:30" и "16:30:15",
 * наружу всегда отдаёт канонический "HH:MM:SS", чтобы сравнение было строковым.
 */
final readonly class TimeOfDay implements \Stringable
{
    private function __construct(public string $value)
    {
    }

    public static function fromString(string $value): self
    {
        $value = trim($value);

        if (1 !== preg_match('/^(?<h>[01]\d|2[0-3]):(?<m>[0-5]\d)(?::(?<s>[0-5]\d))?$/', $value, $m)) {
            throw new \InvalidArgumentException(sprintf('Некорректное время суток UTC: "%s"', $value));
        }

        return new self(sprintf('%s:%s:%s', $m['h'], $m['m'], $m['s'] ?? '00'));
    }

    public function secondsSinceMidnight(): int
    {
        [$h, $m, $s] = array_map(intval(...), explode(':', $this->value));

        return $h * 3600 + $m * 60 + $s;
    }

    public function isMidnight(): bool
    {
        return 0 === $this->secondsSinceMidnight();
    }

    public function __toString(): string
    {
        return $this->value;
    }
}

/**
 * Окно оф-пиковой скидки провайдера в UTC.
 *
 * Интервал [start, end): начало включительно, конец исключительно; может
 * переходить через полночь (16:30–00:30) — тогда попадание означает
 * "позже начала ИЛИ раньше конца". Конец "00:00" трактуется как конец суток.
 * Совпадающие начало и конец дают пустое окно (скидки нет никогда).
 *
 * Множитель применяется к стоимости события целиком (TASK п. 3).
 */
final readonly class OffPeakWindow
{
    public function __construct(
        public TimeOfDay $start,
        public TimeOfDay $end,
        public Money $multiplier,
    ) {
        if ($multiplier->isNegative()) {
            throw new \InvalidArgumentException('Множитель оф-пика не может быть отрицательным');
        }
    }

    public static function fromStrings(string $start, string $end, string $multiplier): self
    {
        return new self(TimeOfDay::fromString($start), TimeOfDay::fromString($end), Money::fromString($multiplier));
    }

    public function contains(\DateTimeImmutable $utc): bool
    {
        return $this->containsSecondOfDay(
            ((int) $utc->format('H')) * 3600 + ((int) $utc->format('i')) * 60 + (int) $utc->format('s'),
        );
    }

    public function containsSecondOfDay(int $secondOfDay): bool
    {
        $from = $this->start->secondsSinceMidnight();
        $to = $this->end->secondsSinceMidnight();

        if ($this->end->isMidnight() && !$this->start->isMidnight()) {
            $to = 24 * 3600;
        }

        if ($from === $to) {
            return false;
        }

        if ($from < $to) {
            return $secondOfDay >= $from && $secondOfDay < $to;
        }

        return $secondOfDay >= $from || $secondOfDay < $to;
    }
}
