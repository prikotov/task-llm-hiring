<?php

declare(strict_types=1);

namespace App\DBAL\Types;

use DateTimeInterface;
use DateTimeZone;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;

/**
 * Время в колонке timestamptz, в PHP — каноническая строка
 * «Y-m-d H:i:sO» в UTC (смещение всегда «+0000»).
 *
 * Зачем: строка цен использует valid_from как часть композитного ключа,
 * а ключи ORM обязаны быть скалярными (см. UnitOfWork::getIdHashByIdentifier).
 * Единый формат и смещение сохраняют корректность SQL-сравнений:
 * лексикографическое сравнение строк эквивалентно хронологическому.
 *
 * На входе (DQL-параметры) допускаются и строки, и DateTime/DateTimeImmutable —
 * в обоих случаях значение канонизируется в ту же строку.
 */
final class TzDateStringType extends Type
{
    public const NAME = 'tz_date_string';

    public function getSQLDeclaration(array $column, AbstractPlatform $platform): string
    {
        return 'TIMESTAMPTZ';
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }

    public function getMappedDatabaseTypes(AbstractPlatform $platform): array
    {
        return ['timestamptz'];
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return self::canonize($value);
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): ?string
    {
        if ($value === null) {
            return null;
        }

        return \is_string($value) ? $value : self::canonize($value);
    }

    /**
     * Канонизирует значение: DateTime(Immutable) → «Y-m-d H:i:sO» UTC,
     * строка — пересобирается из разобранных частей (в т.ч. с другим смещением).
     */
    public static function canonize(string|DateTimeInterface $value): string
    {
        if ($value instanceof DateTimeInterface) {
            return $value->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:sO');
        }

        $parsed = \DateTimeImmutable::createFromFormat('Y-m-d H:i:sO', $value)
            ?? new \DateTimeImmutable($value);

        return $parsed->setTimezone(new DateTimeZone('UTC'))->format('Y-m-d H:i:sO');
    }
}
