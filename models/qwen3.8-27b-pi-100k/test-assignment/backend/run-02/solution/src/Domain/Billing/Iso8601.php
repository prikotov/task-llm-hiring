<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Exception\BillingException;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Дату-время в ISO-8601; внутреннее рабочее представление — UTC.
 */
final class Iso8601
{
    /**
     * Допустимые формы: 2026-09-15T11:00:00Z | ...T11:00:00.123456Z |
     * 2026-09-15T11:00:00+02:00 | без смещения (в этом случае — по UTC).
     */
    private const PATTERN = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}(?::\d{2}(?:\.\d{1,6})?)?(?:Z|z|[+-]\d{2}:\d{2})?$/';

    private function __construct()
    {
    }

    public static function parseUtc(string $value): DateTimeImmutable
    {
        if (preg_match(self::PATTERN, $value) !== 1) {
            throw new BillingException(sprintf(
                "Ожидается дата-время ISO-8601 (например, '2026-09-15T11:00:00Z'), дано '%s'",
                $value,
            ));
        }

        try {
            $dateTime = new DateTimeImmutable($value, new DateTimeZone('UTC'));
        } catch (\Exception $exception) {
            throw new BillingException(
                sprintf("Не удалось разобрать дату-время '%s': %s", $value, $exception->getMessage()),
                0,
                $exception,
            );
        }

        // С явным смещением — абсолютно момент времени; без — считаем UTC.
        // Нормализуем результат в UTC.
        return $dateTime->setTimezone(new DateTimeZone('UTC'));
    }

    /** Формат 'Z' — UTC; дробная часть — только если есть. */
    public static function formatUtc(DateTimeImmutable $dateTime): string
    {
        $utc = $dateTime->setTimezone(new DateTimeZone('UTC'));
        $fraction = $utc->format('u') !== '000000' ? '.'.$utc->format('u') : '';

        return $utc->format('Y-m-d\TH:i:s').$fraction.'Z';
    }
}
