<?php

declare(strict_types=1);

namespace App\Support;

use App\Shared\Exception\BadInputException;

/**
 * Строгий парсер ISO-8601 дат (всегда UTC, суффикс «Z»).
 *
 * Формат: YYYY-MM-DDTHH:MM:SS[.до 6 десятичных]Z.
 * Смещений («+03:00»), отсутствующего «Z», дробных секунд длиннее 6 знаков
 * и невалидных календарных дат («2026-02-30») — некорректный ввод.
 */
final class StrictIsoUtc
{
    private const PATTERN = '/^(\d{4}-\d{2}-\d{2})T([01]\d|2[0-3]):([0-5]\d):([0-5]\d)(?:\.(\d{1,6}))?Z$/';

    public static function parse(string $value): \DateTimeImmutable
    {
        if (preg_match(self::PATTERN, $value, $m) !== 1) {
            throw new BadInputException(sprintf(
                'Некорректная дата (ожидался ISO-8601 UTC, например 2026-09-15T11:00:00Z): «%s»',
                $value,
            ));
        }

        $hasFraction = isset($m[5]);
        $normalized = sprintf(
            '%sT%s:%s:%s%sZ',
            $m[1],
            $m[2],
            $m[3],
            $m[4],
            $hasFraction ? '.'.str_pad($m[5], 6, '0') : '',
        );

        $format = 'Y-m-d\TH:i:s'.($hasFraction ? '.u' : '').'\Z';
        $dt = \DateTimeImmutable::createFromFormat($format, $normalized);
        if ($dt === false || $dt->format('Y-m-d H:i:s') !== $m[1].' '.$m[2].':'.$m[3].':'.$m[4]) {
            throw new BadInputException(sprintf(
                'Некорректная дата (например, несуществующий календарный день): «%s»',
                $value,
            ));
        }

        return $dt;
    }

    /** Канонический вывод (UTC): 2026-09-15T11:00:00Z. */
    public static function format(\DateTimeImmutable $dt): string
    {
        return $dt->format('Y-m-d\TH:i:s\Z');
    }
}
