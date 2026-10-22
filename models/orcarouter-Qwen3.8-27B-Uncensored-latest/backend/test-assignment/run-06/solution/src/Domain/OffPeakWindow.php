<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Оф-пиковое окно провайдера: [start, end) по времени суток UTC,
 * окно может переходить через полночь (16:30–00:30).
 */
final class OffPeakWindow
{
    public const TIME_FORMAT = '/^([01]\d|2[0-3]):[0-5]\d$/';

    private function __construct(
        private readonly string $start,
        private readonly string $end,
    ) {
    }

    public static function parse(string $start, string $end): self
    {
        if (!preg_match(self::TIME_FORMAT, $start) || !preg_match(self::TIME_FORMAT, $end)) {
            throw new DomainException(sprintf(
                'Некорректное оф-пиковое окно "%s"–"%s" (ожидается HH:MM)',
                $start,
                $end,
            ));
        }

        return new self($start, $end);
    }

    /** Входит ли время суток $timeOfDay ('HH:MM', UTC) в окно [start, end)? */
    public function contains(string $timeOfDay): bool
    {
        if ($this->start <= $this->end) {
            return $this->start <= $timeOfDay && $timeOfDay < $this->end;
        }

        // Окно переходит через полночь, например 16:30–00:30.
        return $timeOfDay >= $this->start || $timeOfDay < $this->end;
    }
}
