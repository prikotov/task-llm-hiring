<?php

declare(strict_types=1);

namespace App\Util;

use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use DomainException;

/** Строгий парсинг ISO-8601 с приводением к UTC. */
final class DateTimeUtc
{
    // YYYY-MM-DDTHH:MM[:SS][.frac]  +  (Z | ±HH:MM | ±HHMM)
    private const ISO_RE = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(?::\d{2})?(?:\.\d{1,6})?(?:Z|[+-]\d{2}:?\d{2})?$/';

    /**
     * @throws DomainException
     */
    public static function parse(string $value): DateTimeImmutable
    {
        if (preg_match(self::ISO_RE, $value) !== 1) {
            throw new DomainException(sprintf('Некорректная дата/время (ожидался ISO-8601, например 2026-08-01T00:00:00Z): "%s".', $value));
        }

        try {
            return (new DateTimeImmutable($value))->setTimezone(new DateTimeZone('UTC'));
        } catch (\Throwable $e) {
            throw new DomainException(sprintf('Некорректная дата/время: "%s".', $value), 0, $e);
        }
    }

    public static function format(DateTimeInterface $dateTime): string
    {
        return $dateTime->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
    }

    public static function now(): DateTimeImmutable
    {
        return (new DateTimeImmutable())->setTimezone(new DateTimeZone('UTC'));
    }
}
