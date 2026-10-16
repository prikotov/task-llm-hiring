<?php

declare(strict_types=1);

namespace App\Domain;

use ValueError;

/**
 * Строгий парсер/форматтер ISO-8601 timestamps в UTC.
 */
final class Iso8601
{
    /**
     * Парсит ISO-8601 timestamp в момент UTC.
     *
     * Ожидается формат YYYY-MM-DDTHH:MM:SS[.fraction][Z|±HH:MM];
     * при отсутствии зоны момент считается UTC; смещение ±HH:MM
     * нормализуется к абсолютному моменту.
     *
     * @throws ValueError при некорректном значении
     */
    public static function parseUtc(string $value): \DateTimeImmutable
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}[T ]\d{2}:\d{2}:\d{2}(?:\.\d{1,9})?(?:Z|z|[+-]\d{2}:?(?:\d{2})?)?$/', $value) !== 1) {
            throw new ValueError('Ожидался ISO-8601 timestamp (UTC), получено: ' . $value);
        }

        try {
            return new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        } catch (\Throwable $e) {
            throw new ValueError('Некорректный ISO-8601 timestamp: ' . $value, 0, $e);
        }
    }

    /** Форматирует момент времени как ISO-8601 с суффиксом Z (секундная точность). */
    public static function formatUtc(\DateTimeInterface $timestamp): string
    {
        return \gmdate('Y-m-d\TH:i:s\Z', $timestamp->getTimestamp());
    }
}
