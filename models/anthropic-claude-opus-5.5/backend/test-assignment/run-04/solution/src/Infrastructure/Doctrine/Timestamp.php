<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Перевод моментов времени между доменом (DateTimeImmutable в UTC)
 * и БД (TIMESTAMP(0) WITHOUT TIME ZONE, всегда UTC).
 *
 * Время в сервисе всегда UTC по контракту входных данных, поэтому
 * колонка без таймзоны хранит UTC-«настенное» время. Точность — секунда:
 * события и строки цен задаются с точностью до секунды.
 */
final class Timestamp
{
    public const DB_FORMAT = 'Y-m-d H:i:s';

    public static function toDb(?DateTimeImmutable $value): ?string
    {
        return $value?->setTimezone(new DateTimeZone('UTC'))->format(self::DB_FORMAT);
    }

    public static function fromDb(?string $value): ?DateTimeImmutable
    {
        if (null === $value || '' === $value) {
            return null;
        }

        return new DateTimeImmutable($value, new DateTimeZone('UTC'));
    }

    public static function iso(DateTimeImmutable $value): string
    {
        return $value->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
    }
}
