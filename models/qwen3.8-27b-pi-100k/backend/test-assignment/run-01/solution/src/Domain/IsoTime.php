<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;
use DateTimeZone;
use ValueError;

/**
 * Строгий парсинг дат/времён в формате ISO-8601 UTC («...Z») и безопасное
 * отображение значений в SQL/вывод с явным смещением +00:00.
 */
final class IsoTime
{
    private const UTC_ISO_PATTERN = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d{1,6})?Z$/';

    /**
     * @throws ValueError если строка не является корректным UTC-моментом в ISO-8601 (обязательный суфикс Z).
     */
    public static function parseUtc(string $value): DateTimeImmutable
    {
        if (!preg_match(self::UTC_ISO_PATTERN, $value)) {
            throw new ValueError(sprintf('Ожидается ISO-8601 момент времени UTC вида YYYY-MM-DDTHH:MM:SSZ, получен «%s».', $value));
        }

        // DateTimeImmutable дополнительно отклонит формально невалидные даты (например, 31 февраля).
        return new DateTimeImmutable($value)->setTimezone(new DateTimeZone('UTC'));
    }

    /** Человекочитаемое UTC-представление с суффиксом Z. */
    public static function toUtcZString(DateTimeImmutable $dt): string
    {
        return $dt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
    }

    /**
     * Строка для связывания с-column timestamptz в PostgreSQL: явное смещение
     * +00:00 делает сравнение независимым от часового пояса сессии.
     */
    public static function toSqlUtcString(DateTimeImmutable $dt): string
    {
        return $dt->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:sP');
    }
}
