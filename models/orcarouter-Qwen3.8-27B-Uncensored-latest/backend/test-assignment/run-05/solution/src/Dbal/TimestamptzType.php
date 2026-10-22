<?php

declare(strict_types=1);

namespace App\Dbal;

use DateTimeImmutable;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use Exception;

/**
 * PostgreSQL TIMESTAMP WITH TIME ZONE.
 *
 * Значения всегда хранятся и читаются как DateTimeImmutable в UTC.
 * (В DBAL 4 встроенный тип timestamptz отсутствует.)
 */
final class TimestamptzType extends Type
{
    public const NAME = 'timestamptz';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'timestamp with time zone';
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof DateTimeImmutable) {
            return $value->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s.u');
        }

        throw new Exception(sprintf('%s ожидает DateTimeImmutable, получено %s', self::class, get_debug_type($value)));
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?DateTimeImmutable
    {
        if ($value === null) {
            return null;
        }
        if ($value instanceof DateTimeImmutable) {
            return $value;
        }

        $value = (string) $value;
        // Сессия БД работает в UTC (см. config/packages/doctrine.yaml):
        // wall-clock значения — это и есть UTC.
        $utc = new \DateTimeZone('UTC');

        if (str_contains($value, '.')) {
            $dt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s.u', $value, $utc);
        } else {
            $dt = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $value, $utc);
        }
        if ($dt === false) {
            try {
                $dt = new DateTimeImmutable($value, $utc);
            } catch (Exception $e) {
                throw new Exception(sprintf('Не удалось разобрать timestamptz "%s": %s', $value, $e->getMessage()), 0, $e);
            }
        }

        return $dt;
    }
}
