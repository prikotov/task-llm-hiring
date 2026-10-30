<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use App\Shared\InputFormatException;

/**
 * Оф-пиковое окно скидки провайдера (UTC, по времени суток).
 *
 * Окно [windowStart, windowEnd): start включительно, end исключительно;
 * может переходить через полночь (например 16:30–00:30). multiplier —
 * множитель стоимости события, попавшего в окно (десятичная строка).
 */
final class OffPeak
{
    public function __construct(
        public readonly string $windowStart,
        public readonly string $windowEnd,
        private readonly string $multiplier,
    ) {
        self::assertFormat($windowStart);
        self::assertFormat($windowEnd);
    }

    public function multiplier(): string
    {
        return $this->multiplier;
    }

    /** @param \DateTimeImmutable $tsUtc метка времени запроса в UTC */
    public function applies(\DateTimeImmutable $tsUtc): bool
    {
        $t = (int) $tsUtc->format('G') * 60 + (int) $tsUtc->format('i');
        $start = self::toMinutes($this->windowStart);
        $end = self::toMinutes($this->windowEnd);

        if ($start === $end) {
            return false; // пустое окно [x, x)
        }

        if ($start < $end) {
            return $t >= $start && $t < $end;
        }

        return $t >= $start || $t < $end; // окно через полночь
    }

    private static function assertFormat(string $hm): void
    {
        if (preg_match('/^([01][0-9]|2[0-3]):[0-5]\d$/', $hm) !== 1) {
            throw new InputFormatException('окно оф-пика: ожидается формат "HH:MM", получено: '.$hm);
        }
    }

    private static function toMinutes(string $hm): int
    {
        [$h, $m] = explode(':', $hm);

        return ((int) $h) * 60 + (int) $m;
    }
}
