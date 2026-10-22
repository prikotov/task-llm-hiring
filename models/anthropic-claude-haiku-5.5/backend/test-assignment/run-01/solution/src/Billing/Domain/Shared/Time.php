<?php

declare(strict_types=1);

namespace App\Billing\Domain\Shared;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

/** Все моменты времени в системе — в UTC. */
final class Time
{
    private const ISO_PATTERN = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d{1,6})?(Z|[+-]\d{2}:\d{2})$/';

    public static function utc(): DateTimeZone
    {
        return new DateTimeZone('UTC');
    }

    /** Строгий разбор ISO-8601 с обязательным смещением; результат — в UTC. */
    public static function parseIso(string $value): DateTimeImmutable
    {
        if (preg_match(self::ISO_PATTERN, $value) !== 1) {
            throw new InvalidArgumentException(sprintf('Некорректная дата/время ISO-8601: "%s"', $value));
        }

        return (new DateTimeImmutable($value))->setTimezone(self::utc());
    }

    public static function formatIso(DateTimeImmutable $value): string
    {
        return $value->setTimezone(self::utc())->format('Y-m-d\TH:i:s\Z');
    }
}
