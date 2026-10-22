<?php

declare(strict_types=1);

namespace App\Support;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Конвертация UTC-момента в неambiguous строку для передачи параметром в
 * «сырой» SQL (timestamptz). Всегда с явным офсетом +00:00 — сравнение с
 * тimestamptz корректно вне зависимости от сессионного таймзона БД.
 */
final class SqlDateTime
{
    public static function utc(DateTimeImmutable $dt): string
    {
        return $dt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:sP');
    }

    /** null проецируется в null (для IS NULL в SQL). */
    public static function utcOrNull(?DateTimeImmutable $dt): ?string
    {
        return $dt === null ? null : self::utc($dt);
    }
}
