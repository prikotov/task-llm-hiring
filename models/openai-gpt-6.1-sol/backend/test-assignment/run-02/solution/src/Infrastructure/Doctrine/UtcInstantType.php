<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\DateTimeImmutableType;

/** UTC с микросекундами: стандартный datetime_immutable теряет дробную часть при записи. */
final class UtcInstantType extends DateTimeImmutableType
{
    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'billing_utc_instant';
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }
        if (!$value instanceof \DateTimeImmutable) {
            throw new \InvalidArgumentException('Ожидался неизменяемый момент времени.');
        }

        return $value->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d H:i:s.u');
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?\DateTimeImmutable
    {
        if ($value === null || $value instanceof \DateTimeImmutable) {
            return $value;
        }

        return new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
    }
}
