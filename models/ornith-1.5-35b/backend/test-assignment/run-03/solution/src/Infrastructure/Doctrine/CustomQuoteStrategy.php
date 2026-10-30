<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\DefaultQuoteStrategy;
use Doctrine\ORM\Mapping\JoinColumnMapping;
use Doctrine\ORM\Mapping\ManyToManyOwningSideMapping;
use Doctrine\ORM\Mapping\QuoteStrategy;

/**
 * Кастомная стратегия экранирования идентификаторов Doctrine ORM.
 *
 * Наследует поведение {@see DefaultQuoteStrategy} и обязательно обрачивает
 * вычисляемые имя таблицы, столбца и FK-колонки в двойные кавычки через
 * $platform->quoteSingleIdentifier().
 *
 * В DBAL/ORM нового семейства (DBAL/ORM 4.x) зарезервированные имена столбцов
 * («user») не экранируются автоматически — ORM-персистор генерирует INSERT/UPDATE
 * «голыми» идентификаторами; запросы падают с ошибкой синтаксиса.
 * Экранирование на уровне QuoteStrategy охватывает и CREATE TABLE
 * (миграции Doctrine, buildSchema), и сгенерированные INSERT/UPDATE — одним
 * механизмом, независимо от mapping-флагов.
 */
final class CustomQuoteStrategy implements QuoteStrategy
{
    private DefaultQuoteStrategy $inner;

    public function __construct()
    {
        $this->inner = new DefaultQuoteStrategy();
    }

    public function getColumnName(string $fieldName, ClassMetadata $class, AbstractPlatform $platform): string
    {
        $name = $this->inner->getColumnName($fieldName, $class, $platform);

        return $this->quote($name, $platform);
    }

    public function getTableName(ClassMetadata $class, AbstractPlatform $platform): string
    {
        $name = $this->inner->getTableName($class, $platform);

        return $this->quote($name, $platform);
    }

    public function getSequenceName(array $definition, ClassMetadata $class, AbstractPlatform $platform): string
    {
        $name = $this->inner->getSequenceName($definition, $class, $platform);

        return $this->quote($name, $platform);
    }

    public function getJoinTableName(
        ManyToManyOwningSideMapping $association,
        ClassMetadata $class,
        AbstractPlatform $platform,
    ): string {
        $name = $this->inner->getJoinTableName($association, $class, $platform);

        return $this->quote($name, $platform);
    }

    public function getJoinColumnName(JoinColumnMapping $joinColumn, ClassMetadata $class, AbstractPlatform $platform): string
    {
        $name = $this->inner->getJoinColumnName($joinColumn, $class, $platform);

        return $this->quote($name, $platform);
    }

    public function getReferencedJoinColumnName(
        JoinColumnMapping $joinColumn,
        ClassMetadata $class,
        AbstractPlatform $platform,
    ): string {
        $name = $this->inner->getReferencedJoinColumnName($joinColumn, $class, $platform);

        return $this->quote($name, $platform);
    }

    public function getIdentifierColumnNames(ClassMetadata $class, AbstractPlatform $platform): array
    {
        $names = $this->inner->getIdentifierColumnNames($class, $platform);

        return array_map(fn (string $n): string => $this->quote($n, $platform), $names);
    }

    public function getColumnAlias(
        string $columnName,
        int $counter,
        AbstractPlatform $platform,
        ClassMetadata|null $class = null,
    ): string {
        $name = $this->inner->getColumnAlias($columnName, $counter, $platform, $class);

        return $this->quote($name, $platform);
    }

    private function quote(string $name, AbstractPlatform $platform): string
    {
        if (trim($name) === '' || str_contains($name, '.')) {
            return $name;
        }

        // Имя без qualified-prefix — экранируем целиком. Для
        // 'schema.name' вызывался бы inner->quoteSingleIdentifier, что
        // Doctrine делает сам по флагу quotes; здесь оставляем как есть,
        // чтобы не ломать qualified-ссылки.
        if (! str_contains($name, '.')) {
            return $platform->quoteSingleIdentifier($name);
        }

        // 'schema.name' — для schema-qualified имён экранируем отдельно.
        preg_match('~^(.*?)\.(.*)$~', $name, $m);
        $schema = $m[1] ?? '';
        $column = $m[2] ?? $schema;

        return $platform->quoteSingleIdentifier($schema)
            . '.'
            . $platform->quoteSingleIdentifier($column);
    }
}
