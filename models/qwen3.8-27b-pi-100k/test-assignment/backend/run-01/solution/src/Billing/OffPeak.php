<?php

declare(strict_types=1);

namespace App\Billing;

use App\Support\Decimals;

/**
 * Оф-пик окно провайдера по UTC: [start, end), может пересекать полночь
 * (например 16:30–00:30). start включительно, end исключительно.
 * start == end — пустое окно (скидка не применяется).
 */
final class OffPeak
{
    private const TIME_PATTERN = '/^([01]\d|2[0-3]):[0-5]\d$/';

    private readonly int $startMinutes;
    private readonly int $endMinutes;
    private readonly string $multiplier;

    public function __construct(string $startHhMm, string $endHhMm, string $multiplier)
    {
        if (!preg_match(self::TIME_PATTERN, $startHhMm) || !preg_match(self::TIME_PATTERN, $endHhMm)) {
            throw new \InvalidArgumentException(
                sprintf('Некорректное время оф-пик окна "%s"–"%s", ожидается HH:MM', $startHhMm, $endHhMm),
            );
        }

        Decimals::assertDecimal($multiplier);
        if (Decimals::normalize($multiplier) === '0') {
            throw new \InvalidArgumentException('Множитель оф-пик должен быть положительным.');
        }

        $this->startMinutes = ((int) substr($startHhMm, 0, 2)) * 60 + (int) substr($startHhMm, 3, 2);
        $this->endMinutes = ((int) substr($endHhMm, 0, 2)) * 60 + (int) substr($endHhMm, 3, 2);
        $this->multiplier = Decimals::normalize($multiplier);
    }

    /** Попадание момента события (UTC) в окно скидки. */
    public function contains(\DateTimeImmutable $utcTs): bool
    {
        $minutes = ((int) $utcTs->format('G')) * 60 + (int) $utcTs->format('i');

        return $this->containsMinutes($minutes);
    }

    public function containsMinutes(int $minutes): bool
    {
        if ($this->startMinutes === $this->endMinutes) {
            return false;
        }
        if ($this->startMinutes < $this->endMinutes) {
            return $minutes >= $this->startMinutes && $minutes < $this->endMinutes;
        }

        return $minutes >= $this->startMinutes || $minutes < $this->endMinutes;
    }

    public function getMultiplier(): string
    {
        return $this->multiplier;
    }
}
