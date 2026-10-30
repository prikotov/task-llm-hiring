<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use DateTimeImmutable;
use DateTimeZone;

/**
 * Разбор ISO-8601 (UTC) полей `ts`/`valid_from`/периода отчёта.
 */
final class IsoDateTime
{
    /**
     * Строго ISO-8601 (UTC). Возвращает DateTimeImmutable в UTC.
     */
    public static function parseUtc(string $value): DateTimeImmutable
    {
        if (!preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:\d{2})?$/', $value)) {
            throw new \DomainException(sprintf('Некорректная ISO-8601 дата: "%s"', $value));
        }

        try {
            $dt = new DateTimeImmutable($value, new DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new \DomainException(sprintf('Некорректная ISO-8601 дата: "%s"', $value));
        }

        return $dt->setTimezone(new DateTimeZone('UTC'));
    }

    /** Формат ISO-8601 в UTC (для служебных сообщений). */
    public static function formatUtc(DateTimeImmutable $dt): string
    {
        return $dt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
    }
}
