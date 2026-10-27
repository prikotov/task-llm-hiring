<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Утилиты времени: ISO-8601 всегда в UTC (с суффиксом Z).
 */
final class IsoUtc
{
    private const ISO_UTC = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,6})?Z$/';

    private function __construct()
    {
    }

    /**
     * Парсинг ISO-8601 UTC строки в DateTimeImmutable (внутренний формат UTC).
     */
    public static function parse(string $value): \DateTimeImmutable
    {
        if (!preg_match(self::ISO_UTC, $value)) {
            throw new \ValueError(sprintf('Ожидался ISO-8601 в UTC (с суффиксом Z), фактически: "%s"', $value));
        }

        // Строка уже в UTC; привязываем явную UTC timezone.
        $dt = new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        if ($dt === false) {
            throw new \ValueError(sprintf('Не удалось разобрать время "%s"', $value));
        }

        return $dt;
    }

    /**
     * Форматирует DateTimeImmutable в ISO-8601 UTC с суффиксом Z.
     */
    public static function format(\DateTimeInterface $dt): string
    {
        return $dt->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
    }

    /**
     * Возвращает объект в UTC (для внутренних вычислений по времени суток и т.п.)
     */
    public static function toUtc(\DateTimeInterface $dt): \DateTimeInterface
    {
        if ($dt instanceof \DateTimeImmutable) {
            return $dt->setTimezone(new \DateTimeZone('UTC'));
        }

        $clone = clone $dt;

        return $clone->setTimezone(new \DateTimeZone('UTC'));
    }
}
