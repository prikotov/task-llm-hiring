<?php

declare(strict_types=1);

namespace App\Pricing\Domain;

use App\Shared\Decimal;
use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Окно оф-пика по времени суток UTC: [start, end), может переходить через полночь.
 */
final readonly class OffPeakWindow
{
    private function __construct(
        public int $startMinute,
        public int $endMinute,
        public Decimal $multiplier,
    ) {
    }

    public static function create(string $start, string $end, string $multiplier): self
    {
        $m = Decimal::of($multiplier);
        if ($m->isNegative()) {
            throw new InvalidArgumentException('Множитель оф-пика не может быть отрицательным');
        }

        return new self(self::parseHm($start), self::parseHm($end), $m);
    }

    public static function fromStored(int $startMinute, int $endMinute, string $multiplier): self
    {
        return new self($startMinute, $endMinute, Decimal::of($multiplier));
    }

    public function contains(DateTimeImmutable $ts): bool
    {
        $utc = $ts->setTimezone(new DateTimeZone('UTC'));
        $sec = (int) $utc->format('G') * 3600 + (int) $utc->format('i') * 60 + (int) $utc->format('s');
        $start = $this->startMinute * 60;
        $end = $this->endMinute * 60;

        if ($start === $end) {
            return false; // пустое окно
        }

        return $start < $end
            ? $sec >= $start && $sec < $end
            : $sec >= $start || $sec < $end; // через полночь
    }

    private static function parseHm(string $hm): int
    {
        if (preg_match('/^([01]\d|2[0-3]):([0-5]\d)$/', $hm, $m) !== 1) {
            throw new InvalidArgumentException(sprintf('Некорректное время окна "%s", ожидается HH:MM', $hm));
        }

        return (int) $m[1] * 60 + (int) $m[2];
    }
}
