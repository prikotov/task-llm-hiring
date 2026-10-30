<?php

declare(strict_types=1);

namespace App\Shared;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

final class UtcTime
{
    public static function parse(string $iso): DateTimeImmutable
    {
        $dt = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:sP', $iso)
            ?: DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:s.uP', $iso);
        $errors = DateTimeImmutable::getLastErrors();
        if ($dt === false || ($errors !== false && $errors['warning_count'] > 0)) {
            throw new InvalidArgumentException(sprintf('Некорректная дата ISO-8601: "%s"', $iso));
        }

        return $dt->setTimezone(new DateTimeZone('UTC'));
    }

    public static function format(DateTimeImmutable $dt): string
    {
        return $dt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
    }
}
