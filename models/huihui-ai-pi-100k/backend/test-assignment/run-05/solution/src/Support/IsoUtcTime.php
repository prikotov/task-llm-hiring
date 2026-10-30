<?php

declare(strict_types=1);

namespace App\Support;

use App\Application\Exception\InputException;

/**
 * Разбор ISO-8601 времён, всегда в UTC.
 */
final class IsoUtcTime
{
    public static function parse(string $value, string $context): \DateTimeImmutable
    {
        try {
            $dt = new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new InputException(
            sprintf('%s: не удалось распознать время ISO-8601: "%s"', $context, $value),
            0,
            $e,
        );
        }

        if ($dt->getTimezone()->getOffset($dt) !== 0) {
            throw new InputException(sprintf('%s: ожидается время UTC (смещение 0): "%s"', $context, $value));
        }

        return $dt;
    }
}
