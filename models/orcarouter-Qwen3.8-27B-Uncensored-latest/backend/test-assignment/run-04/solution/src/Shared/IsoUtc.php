<?php

declare(strict_types=1);

namespace App\Shared;

/**
 * Strict ISO-8601 UTC date-time handling (always "Z" suffix).
 */
final class IsoUtc
{
    private const REGEX = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?Z$/';

    private function __construct()
    {
    }

    public static function parse(string $value): \DateTimeImmutable
    {
        if (\preg_match(self::REGEX, $value) !== 1) {
            throw new ImportException(\sprintf('"%s" is not ISO-8601 UTC, e.g. "2026-09-01T00:00:00Z"', $value));
        }

        $dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $value);
        if ($dt === false) {
            $dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.u\Z', $value);
        }

        if ($dt === false) {
            throw new ImportException(\sprintf('"%s" is not a valid date-time', $value));
        }

        return $dt;
    }

    public static function format(\DateTimeImmutable $dt): string
    {
        return $dt->format('Y-m-d\TH:i:s\Z');
    }
}
