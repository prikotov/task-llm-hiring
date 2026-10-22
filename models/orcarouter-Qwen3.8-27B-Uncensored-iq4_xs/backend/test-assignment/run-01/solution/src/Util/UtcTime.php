<?php

declare(strict_types=1);

namespace App\Util;

use App\Exception\ImportException;
use DateTimeImmutable;

final class UtcTime
{
    /** Форматы разбора: секунды и наносекунд; P — Z / ±hh:mm. */
    private const PARSE_FORMATS = ['Y-m-d\TH:i:sP', 'Y-m-d\TH:i:s.vP', 'Y-m-d\TH:i:s.uP'];

    /**
     * Разбор ISO-8601 (UTC) в DateTimeImmutable.
     *
     * Принимаются формы RFC-3339 (включая дробные секунды) с зонами
     * Z / +00:00; любое время, не эквивалентное UTC, — ошибка.
     */
    public static function parse(mixed $value, string $context): DateTimeImmutable
    {
        if (!\is_string($value) || trim($value) === '') {
            throw new ImportException(sprintf('%s: ожидается ISO-8601 (UTC), получено: %s', $context, var_export($value, true)));
        }

        foreach (self::PARSE_FORMATS as $format) {
            $parsed = DateTimeImmutable::createFromFormat($format, $value);
            if ($parsed instanceof DateTimeImmutable 
                && in_array($parsed->getTimezone()->getName(), ['UTC', '+00:00', 'Z'], true)
            ) {
                return $parsed;
            }
        }

        throw new ImportException(sprintf('%s: ожидается ISO-8601 (UTC), получено: %s', $context, var_export($value, true)));
    }

    /** Формат вывода ts в сообщениях: ISO-8601 с "Z" (без нулевых наносекунд). */
    public static function format(DateTimeImmutable $dt): string
    {
        $utc = $dt->setTimezone(new \DateTimeZone('UTC'));
        if ((int) $utc->format('v') === 0) {
            return $utc->format('Y-m-d\TH:i:s\Z');
        }

        return $utc->format('Y-m-d\TH:i:s.v\Z');
    }
}
