<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Оф-пиковое окно провайдера (UTC, минуты внутри суток) и множитель цены.
 *
 * Окно задаётся [start, end) — включительно start, исключительно end.
 * Может начинаться в одном календарном дне и переходить через полночь
 * (например, 16:30 -> 00:30). start == end трактуется как пустое окно.
 */
final class OffPeak
{
    public function __construct(
        public readonly int $startMin,
        public readonly int $endMin,
        public readonly string $multiplier,
    ) {
    }

    /**
     * Парсит окно из пары «HH:MM» + строки-множитель.
     */
    public static function fromWindowAndMultiplier(string $start, string $end, string $multiplier): self
    {
        return new self(self::minutesFromHM($start), self::minutesFromHM($end), $multiplier);
    }

    /**
     * Попадает ли момент (минуты внутри суток UTC) в оф-пик.
     */
    public function appliesAt(int $minuteOfDay): bool
    {
        if ($this->startMin === $this->endMin) {
            return false;                          // пустое окно [x, x)
        }

        if ($this->startMin < $this->endMin) {
            return $minuteOfDay >= $this->startMin && $minuteOfDay < $this->endMin;
        }

        // Переход через полночь: либо после начала, либо до конца.
        return $minuteOfDay >= $this->startMin || $minuteOfDay < $this->endMin;
    }

    public static function minutesFromHM(string $hm): int
    {
        if (!preg_match('/^([01]?\d|2[0-3]):([0-5]\d)$/', trim($hm), $m)) {
            throw new \DomainException('Bad HH:MM value: ' . $hm);
        }

        return (int) $m[1] * 60 + (int) $m[2];
    }
}
