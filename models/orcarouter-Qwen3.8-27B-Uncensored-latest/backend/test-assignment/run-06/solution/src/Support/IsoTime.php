<?php

declare(strict_types=1);

namespace App\Support;

use App\Domain\DomainException;

/** Строгий разбор/форматирование ISO-8601 UTC-моментов. */
final class IsoTime
{
    private function __construct()
    {
    }

    public static function parseUtc(string $value, string $what): \DateTimeImmutable
    {
        $pattern = '/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(\.\d{1,6})?Z$/';
        if (preg_match($pattern, $value, $m) !== 1) {
            throw new DomainException(sprintf('%s: "%s" не является ISO-8601 UTC (формат YYYY-MM-DDTHH:MM:SS[.ffffff]Z)', $what, $value));
        }

        $base = sprintf('%s-%s-%sT%s:%s:%s', $m[1], $m[2], $m[3], $m[4], $m[5], $m[6]);
        $date = \DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:s', $base, new \DateTimeZone('UTC'))
            ?? throw new DomainException(sprintf('%s: не удалось разобрать "%s"', $what, $value));

        // Валидация реального календарного момента (например, 2026-02-30 отклоняется).
        if ($date->format('Y-m-d\TH:i:s') !== $base) {
            throw new DomainException(sprintf('%s: "%s" не является корректным моментом во времени', $what, $value));
        }

        // Дробные секунды (до 6 знаков = микросекунды).
        if (isset($m[7]) && $m[7] !== '') {
            $micros = substr(str_pad(ltrim($m[7], '.'), 6, '0', STR_PAD_LEFT), 0, 6);
            $date = new \DateTimeImmutable(sprintf('%s.%sZ', $base, $micros), new \DateTimeZone('UTC'));
        }

        return $date;
    }

    public static function formatUtc(\DateTimeImmutable $dt): string
    {
        return $dt->format('Y-m-d\TH:i:s\Z');
    }
}
