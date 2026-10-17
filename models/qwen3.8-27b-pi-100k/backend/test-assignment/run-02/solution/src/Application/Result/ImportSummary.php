<?php

declare(strict_types=1);

namespace App\Application\Result;

/**
 * Итог импорта справочника цен.
 */
final class ImportSummary
{
    public function __construct(
        public readonly int $providers,
        public readonly int $models,
        public readonly int $prices,
    ) {
    }
}
