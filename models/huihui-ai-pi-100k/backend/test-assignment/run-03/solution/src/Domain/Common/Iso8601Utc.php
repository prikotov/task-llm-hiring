<?php

declare(strict_types=1);

namespace App\Domain\Common;

use DateException;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Разбор ISO-8601-времени с явным часовым поясом (Z или смещение)
 * в мгновенный момент времени в UTC.
 *
 * Разбор намеренно не опирается на «щедрую» дату PHP: поименованные группы
 * сверяются с результатом построения, поэтому «2026-02-30» или «25:00»
 * считаются некорректными.
 */
final class Iso8601Utc
{
    private const PATTERN = '/^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2})(?::(\d{2})(?:\.\d+)?)?(?:[Zz]|([+-])(\d{2}):?(\d{2}))$/s';

    /** @throws InvalidArgumentException при некорректной строке */
    public static function parse(string $value): DateTimeImmutable
    {
        $value = trim($value);
        if (!preg_match(self::PATTERN, $value, $m)) {
            throw new InvalidArgumentException(sprintf('Ожидается время в формате ISO-8601 с часовым поясом: "%s"', $value));
        }

        $year = (int) $m[1];
        $month = (int) $m[2];
        $day = (int) $m[3];
        $hour = (int) $m[4];
        $minute = (int) $m[5];
        $second = (int) ($m[6] ?? '00');
        $wall = sprintf('%04d-%02d-%02d %02d:%02d:%02d', $year, $month, $day, $hour, $minute, $second);

        try {
            $utc = new DateTimeImmutable($wall, new DateTimeZone('UTC'));
        } catch (DateException $e) {
            throw new InvalidArgumentException(sprintf('Некорректное время "%s": %s', $value, $e->getMessage()), 0, $e);
        }

        // Защита от «переката» дат (30 февраля и т.п.): PHP строит дату молча.
        if ($utc->format('Y-m-d H:i:s') !== $wall) {
            throw new InvalidArgumentException(sprintf('Некорректное время "%s"', $value));
        }

        // Смещение: для «Z» — ноль, иначе — явное «+HH:MM» / «-HHMM».
        $offsetMinutes = 0;
        if ($value !== '' && $value[strlen($value) - 1] !== 'Z' && $value[strlen($value) - 1] !== 'z') {
            $offsetMinutes = ((int) $m[8]) * 60 + ((int) $m[9]);
            if ($m[7] === '-') {
                $offsetMinutes = -$offsetMinutes;
            }
        }
        // Мгновенное время UTC = местное время − смещение.
        $utc = $utc->modify(sprintf('%+d minutes', -($offsetMinutes)));

        return $utc;
    }

    /**
     * Переводит datetime, прочитанный из БД (timestamptz), в явный UTC.
     * Сессия PostgreSQL отдаёт момент в UTC, а DateTimeTz-тип Doctrine
     * достраивает значение implicit-часовым поясом — чтобы семантика
     * не зависела от ini, мы явностью закрепляем UTC.
     */
    public static function reanchor(DateTimeInterface $value): DateTimeImmutable
    {
        return $value instanceof DateTimeImmutable
            ? $value->setTimezone(new DateTimeZone('UTC'))
            : (new DateTimeImmutable($value->format('Y-m-d H:i:s.u')))->setTimezone(new DateTimeZone('UTC'));
    }

    /**
     * DBAL 4.5 в типе datetimetz принимает только неизменяемый
     * \DateTime (не DateTimeImmutable) — конвертируем значение
     * перед записью в сущность.
     */
    public static function toMutableUtc(DateTimeInterface $value): \DateTime
    {
        return (new \DateTime('@' . (string) $value->getTimestamp()))->setTimezone(new DateTimeZone('UTC'));
    }
}
