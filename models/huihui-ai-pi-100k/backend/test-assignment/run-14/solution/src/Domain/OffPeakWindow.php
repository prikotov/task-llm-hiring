<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Окно оф-пиковой скидки провайдера по времени суток UTC.
 *
 * Окно [startMinutes, endMinutes) в пределах 0..1439; может переходить через
 * полночь (start > end). Множитель стоимости применяется к событию, попавшему
 * в окно, по правилу п. 3 задания.
 *
 * @param int    $startMinutes минуты суток UTC, включительно (0..1439)
 * @param int    $endMinutes   минуты суток UTC, эксклюзивно (0..1439);
 *                             1440 допустимо как "24:00/00:00" (окно до конца дня)
 * @param string $multiplier   десятичная строка (bcmath-scale), > 0
 */
final class OffPeakWindow
{
    public function __construct(
        private readonly int $startMinutes,
        private readonly int $endMinutes,
        private readonly string $multiplier,
    ) {
        if (!\in_array($multiplier, ['0', '1'], true) && bccomp($multiplier, '0', 16) < 0) {
            throw new \DomainException('Оф-пиковый множитель должен быть неотрицательным.');
        }
    }

    /** Окно [start, end); может переходить через полночь. */
    public function contains(int $minutesOfDayUtc): bool
    {
        if ($this->startMinutes === $this->endMinutes) {
            return false; // полное окно — деградация до отключённого
        }
        if ($this->startMinutes < $this->endMinutes) {
            return $minutesOfDayUtc >= $this->startMinutes && $minutesOfDayUtc < $this->endMinutes;
        }

        return $minutesOfDayUtc >= $this->startMinutes || $minutesOfDayUtc < $this->endMinutes;
    }

    public function getMultiplier(): string
    {
        return $this->multiplier;
    }

    public function startMinutes(): int
    {
        return $this->startMinutes;
    }

    public function endMinutes(): int
    {
        return $this->endMinutes;
    }

    /** Строка "HH:MM" для label/логирования. */
    public function windowStartLabel(): string
    {
        return sprintf('%02d:%02d', intdiv($this->startMinutes, 60), $this->startMinutes % 60);
    }

    public function windowEndLabel(): string
    {
        $end = $this->endMinutes % 1440;

        return sprintf('%02d:%02d', intdiv($end, 60), $end % 60);
    }
}
