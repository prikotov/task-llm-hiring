<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Разбор ISO-8601 моментов времени, всегда UTC (суффикс З).
 * Допускаются секунды и, опционально, доли секунды.
 */
final class DateTimeParser
{
    private const PATTERN = '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(\.\d{1,6})?Z$/';

    private function __construct()
    {
    }

    /**
     * @throws \DomainException
     */
    public static function parseUtc(string $context, string $value): \DateTimeImmutable
    {
        if (!preg_match(self::PATTERN, $value, $m)) {
            throw new \DomainException(sprintf('%s: ожидается ISO-8601 UTC вида 2026-08-01T00:00:00Z', $context));
        }

        $dt = new \DateTimeImmutable(
            sprintf('%s-%s-%s %s:%s:%s UTC', $m[1], $m[2], $m[3], $m[4], $m[5], $m[6]),
            new \DateTimeZone('UTC'),
        );
        $micros = isset($m[7]) && $m[7] !== '' ? ltrim(substr($m[7], 1), '0') : '';

        return $dt->modify('+'.str_pad($micros, 6, '0', STR_PAD_LEFT).' microseconds');
    }
}
