<?php

declare(strict_types=1);

namespace App\Ingest;

/**
 * Результат импорта журнала: принято / пропущено как дубли.
 *
 * @readonly
 */
final class UsageImportResult
{
    public function __construct(
        public readonly int $imported,
        public readonly int $duplicates,
    ) {
    }
}
