<?php

declare(strict_types=1);

namespace App\Shared\Domain;

use DateTimeImmutable;
use DateTimeZone;
use InvalidArgumentException;

/**
 * Разбор и форматирование моментов времени ISO-8601. Внутри системы все
 * моменты — DateTimeImmutable в UTC с точностью до секунды.
 */
final class Iso8601
{
    private const string PATTERN = '/^(\d{4}-\d{2}-\d{2})[T ](\d{2}:\d{2}:\d{2})(\.\d+)?(Z|[+-]\d{2}:?\d{2})$/i';

    /**
     * @param bool $truncateFraction дробные секунды отбрасываются (true) или
     *                               считаются ошибкой (false)
     */
    public static function parse(string $value, bool $truncateFraction = false): DateTimeImmutable
    {
        if (preg_match(self::PATTERN, $value, $m) !== 1) {
            throw new InvalidArgumentException(sprintf('"%s" — не момент времени ISO-8601 с часовым поясом (ожидается, например, 2026-09-15T12:00:00Z).', $value));
        }
        if ($m[3] !== '' && !$truncateFraction) {
            throw new InvalidArgumentException(sprintf('"%s": дробные секунды не поддерживаются.', $value));
        }

        $zone = strtoupper($m[4]) === 'Z' ? '+00:00' : $m[4];
        $normalized = $m[1].'T'.$m[2].$zone;
        $parsed = DateTimeImmutable::createFromFormat('!Y-m-d\TH:i:sP', $normalized);
        // createFromFormat «исправляет» переполнения (25:00, 31.02) — отсекаем.
        if ($parsed === false || $parsed->format('Y-m-d\TH:i:s') !== $m[1].'T'.$m[2]) {
            throw new InvalidArgumentException(sprintf('"%s" — некорректная дата/время.', $value));
        }

        return $parsed->setTimezone(new DateTimeZone('UTC'));
    }

    public static function format(DateTimeImmutable $moment): string
    {
        return $moment->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
    }
}
