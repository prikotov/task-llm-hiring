<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Разбор ISO-8601 дат (UTC) к использованию в предметной области.
 */
final class IsoTime
{
    private const UTC = 'UTC';

    public static function parseUtc(string $iso): DateTimeImmutable
    {
        $trimmed = trim($iso);

        $dt = DateTimeImmutable::createFromFormat(\DateTimeInterface::ISO8601, $trimmed);
        if ($dt === false) {
            // Разрешаем также дробные секунды.
            $dt = DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.v\Z', $trimmed);
        }

        if ($dt === false) {
            throw new \RuntimeException('invalid ISO-8601 datetime: ' . $iso);
        }

        return $dt->setTimezone(new DateTimeZone(self::UTC));
    }
}
