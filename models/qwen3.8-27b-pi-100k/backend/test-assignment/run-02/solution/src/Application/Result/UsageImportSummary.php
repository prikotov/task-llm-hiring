<?php

declare(strict_types=1);

namespace App\Application\Result;

/**
 * Итог импорта событий использования.
 */
final class UsageImportSummary
{
    public function __construct(
        public readonly int $lines,
        public readonly int $created,
        public readonly int $duplicates,
    ) {
    }
}
