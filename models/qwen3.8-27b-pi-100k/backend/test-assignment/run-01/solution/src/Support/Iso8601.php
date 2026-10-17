<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Разбор ISO-8601 с обязательным UTC-суффиксом (Z или +00:00).
 * Результат всегда \DateTimeImmutable в UTC. Числовые поля проверяются
 * (например, 2026-02-31 отклоняется).
 */
final class Iso8601
{
    public const FORMATS = [
        'Y-m-d\TH:i:s\Z',
        'Y-m-d\TH:i:s\+00:00',
        'Y-m-d\TH:i:s.v\Z',
        'Y-m-d\TH:i:s.v\+00:00',
    ];

    final private function __construct()
    {
    }

    public static function parseUtc(string $value): \DateTimeImmutable
    {
        $utc = new \DateTimeZone('UTC');

        foreach (self::FORMATS as $format) {
            $dt = \DateTimeImmutable::createFromFormat($format, $value, $utc);
            if ($dt !== false) {
                $errors = \DateTimeImmutable::getLastErrors();
                $bad = ($errors['error_count'] ?? 0) > 0;
                if (!$bad) {
                    return $dt;
                }
            }
        }

        throw new \InvalidArgumentException(
            sprintf('Ожидается ISO-8601 datetime в UTC (напр. 2026-09-15T12:00:00Z), получено: "%s"', $value),
        );
    }

    public static function formatUtc(\DateTimeInterface $dt): string
    {
        return $dt->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z');
    }
}
