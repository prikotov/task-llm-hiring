<?php

declare(strict_types=1);

namespace App\Domain;

final readonly class OffPeak
{
    public function __construct(public string $start, public string $end, public string $multiplier)
    {
        foreach ([$start, $end] as $time) {
            if (!preg_match('/\A(?:[01][0-9]|2[0-3]):[0-5][0-9]\z/', $time)) {
                throw new \InvalidArgumentException('Границы оф-пика должны иметь формат HH:MM.');
            }
        }
        if (!preg_match('/\A[0-9]+(?:\.[0-9]+)?\z/', $multiplier)) {
            throw new \InvalidArgumentException('Множитель должен быть неотрицательной десятичной строкой.');
        }
    }

    public function contains(\DateTimeImmutable $instant): bool
    {
        $time = $instant->setTimezone(new \DateTimeZone('UTC'))->format('H:i');

        // Одинаковые границы задают пустое окно, а не круглосуточную скидку.
        return $this->start < $this->end
            ? $time >= $this->start && $time < $this->end
            : $this->start > $this->end && ($time >= $this->start || $time < $this->end);
    }
}
