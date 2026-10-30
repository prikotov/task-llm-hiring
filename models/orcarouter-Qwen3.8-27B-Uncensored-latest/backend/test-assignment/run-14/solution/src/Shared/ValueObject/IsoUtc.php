<?php

declare(strict_types=1);

namespace App\Shared\ValueObject;

use DateTimeImmutable;
use ValueError;

/**
 * ISO-8601 UTC helpers.
 *
 * Canonical form is "YYYY-MM-DDTHH:MM:SS+00:00" (second precision, always
 * UTC). It is lexicographically comparable — the basis for the rule-1
 * price-row pick and the report period filter.
 */
final class IsoUtc
{
    /**
     * Normalise any valid ISO-8601 timestamp (Z / ±HH:MM / bare UTC)
     * to the canonical form. Accepts "YYYY-MM-DD HH:MM:SS+00" (the
     * form PostgreSQL returns for timestamptz columns).
     *
     * @throws ValueError when the value is not a valid timestamp.
     */
    public static function canonical(string $value): string
    {
        $value = trim($value);
        $pattern = '/^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2}):(\d{2})(?:\.\d+)?'
            .'(?:Z|([+-])(\d{2}):?(\d{2})?)?$/';
        if (preg_match($pattern, $value, $m) !== 1) {
            throw new ValueError(sprintf('Invalid ISO-8601 timestamp "%s".', $value));
        }

        [$_, $y, $mo, $d, $h, $mi, $s, $sign, $offH, $offM] = array_pad($m, 10, '');
        if ((int) $mo < 1 || (int) $mo > 12
            || (int) $d < 1 || (int) $d > 31
            || (int) $h > 23 || (int) $mi > 59 || (int) $s > 60
        ) {
            throw new ValueError(sprintf('Invalid ISO-8601 timestamp "%s".', $value));
        }

        $sign = $sign === '-' ? -1 : 1;
        $offH = (int) $offH;
        $offM = (int) $offM;

        $dt = new DateTimeImmutable(sprintf(
            '%04d-%02d-%02d %02d:%02d:%02d',
            (int) $y, (int) $mo, (int) $d, (int) $h, (int) $mi,
            min((int) $s, 59)
        ));
        if ((int) $s === 60) {
            $dt = $dt->modify('+1 second');
        }
        // Convert from the given UTC offset to true UTC.
        $dt = $dt->modify(sprintf('%+03d minutes', -$sign * ($offH * 60 + $offM)));

        return $dt->format('Y-m-d\TH:i:s+00:00');
    }

    /**
     * Postgres timestamptz → canonical.
     */
    public static function fromDb(string $value): string
    {
        return self::canonical($value);
    }
}
