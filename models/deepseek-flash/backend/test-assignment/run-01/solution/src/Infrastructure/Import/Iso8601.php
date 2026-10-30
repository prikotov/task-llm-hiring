<?php

declare(strict_types=1);

namespace App\Infrastructure\Import;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

final class Iso8601
{
    private const string PATTERN = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,6})?(?:Z|[+-]\d{2}:\d{2})$/';

    public static function parse(string $value, string $field): DateTimeImmutable
    {
        $value = trim($value);

        // A date-only ISO-8601 value is accepted as midnight UTC; this is
        // convenient for report periods such as --from=2026-09-01.
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $value) === 1) {
            $value .= 'T00:00:00Z';
        }

        if (preg_match(self::PATTERN, $value) !== 1) {
            throw new InvalidArgumentException(sprintf('Invalid ISO-8601 value for %s: "%s".', $field, $value));
        }

        try {
            $moment = new DateTimeImmutable($value);
        } catch (\Exception $exception) {
            throw new InvalidArgumentException(sprintf(
                'Invalid ISO-8601 value for %s: "%s".',
                $field,
                $value,
            ), 0, $exception);
        }

        return $moment->setTimezone(new DateTimeZone('UTC'));
    }
}
