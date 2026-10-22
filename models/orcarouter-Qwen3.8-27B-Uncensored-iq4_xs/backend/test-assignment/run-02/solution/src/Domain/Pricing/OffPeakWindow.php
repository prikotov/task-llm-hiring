<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

/**
 * Окно оф-пиковой скидки провайдера по времени суток UTC.
 *
 * Границы — в минутах от полуночи. Старт включительно, конец эксклюзивно:
 * окно «16:30-00:30» действует до 00:30:00. Старт > конец — окно
 * пересекает полночь. Старт === конец — пустое окно (скидки нет).
 * Точность — по минутам: ts 00:05:30 попадает в окно, содержащее 00:05.
 */
final readonly class OffPeakWindow
{
    public function __construct(
        private int $startMin,
        private int $endMin,
        /** Десятичная строка (bcmath), например «0.5». */
        private string $multiplier,
    ) {
    }

    /** Множитель цены в оф-пике (десятичная строка). */
    public function multiplier(): string
    {
        return $this->multiplier;
    }

    /**
     * Попадает ли момент времени (UTC) в окно.
     */
    public function isOffPeak(\DateTimeImmutable $ts): bool
    {
        if ($this->startMin === $this->endMin) {
            return false;
        }

        $minutes = (int) $ts->format('H') * 60 + (int) $ts->format('i');
        if ($minutes < 0 || $minutes > 23 * 60 + 59) {
            return false;
        }

        if ($this->startMin < $this->endMin) {
            return $minutes >= $this->startMin && $minutes < $this->endMin;
        }

        // Окно пересекает полночь: [start, 24:00) ∪ [00:00, end)
        return $minutes >= $this->startMin || $minutes < $this->endMin;
    }

    /**
     * Тот же вопрос, но для минуты дня — удобно для тестов.
     */
    public function covers(int $minutesFromMidnight): bool
    {
        $ts = (new \DateTimeImmutable('1970-01-01 00:00:00+00:00'))->modify(
            '+'.$minutesFromMidnight.' minutes',
        );

        return $this->isOffPeak($ts);
    }
}
