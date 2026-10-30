<?php

declare(strict_types=1);

namespace App\Support;

use DateTimeImmutable;
use DateTimeZone;

/** Строгий разбор ISO-8601: дата + время, опциональные дробные секунды и смещение UTC. */
final class Iso8601
{
    private const PATTERN = '/^(\d{4})-(\d{2})-(\d{2})[T ](\d{2}):(\d{2})(?::(\d{2})(\.\d{1,9})?)?(Z|[+-]\d{2}:\d{2})?$/';

    public static function parseUtc(string $value): DateTimeImmutable
    {
        if (!preg_match(self::PATTERN, $value, $m)) {
            throw new \InvalidArgumentException('Ожидается строка ISO-8601 (UTC)');
        }
        if (!checkdate((int) $m[2], (int) $m[3], (int) $m[1])) {
            throw new \InvalidArgumentException('Некорректная дата');
        }
        if ((int) $m[4] > 23 || (int) $m[5] > 59 || (int) ($m[6] ?? 0) > 59) {
            throw new \InvalidArgumentException('Некорректное время');
        }

        try {
            $normalized = str_replace(' ', 'T', $value);
            $utc = new DateTimeImmutable($normalized, new DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new \InvalidArgumentException($e->getMessage());
        }

        // Схемы тарификации и окна оф-пика оперируют секундами; дробная часть отбрасывается.
        return $utc->setTimezone(new DateTimeZone('UTC'))->setMicrosecond(0);
    }
}
