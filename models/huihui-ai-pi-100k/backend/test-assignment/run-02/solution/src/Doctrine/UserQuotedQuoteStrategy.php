<?php

declare(strict_types=1);

namespace App\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;

/**
 * Цитирует столбцы, которые являются служебными в PostgreSQL
 * (`user` — col_name_keyword: в списках колок CREATE/INSERT/UPDATE без
 * кавычек разбор падает). Остальные идентификаторы — как в дефолтной
 * стратегии.
 */
final class UserQuotedQuoteStrategy extends DefaultQuoteStrategy
{
    private const RESERVED_COLUMN_NAMES = ['user'];

    public function getColumnName(string $fieldName, ClassMetadata $class, AbstractPlatform $platform): string
    {
        $mapping = $class->fieldMappings[$fieldName];
        if (in_array($mapping->columnName, self::RESERVED_COLUMN_NAMES, true)) {
            return $platform->quoteSingleIdentifier($mapping->columnName);
        }

        return $mapping->columnName;
    }
}
