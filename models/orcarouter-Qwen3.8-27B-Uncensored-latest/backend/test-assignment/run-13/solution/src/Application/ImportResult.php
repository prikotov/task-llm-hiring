<?php

declare(strict_types=1);

namespace App\Application;

final class ImportResult
{
    public function __construct(
        public readonly int $importedCount,
        public readonly int $providers = 0,
        public readonly int $models = 0,
        public readonly int $prices = 0,
        public readonly int $usageEvents = 0,
        public readonly array $errors = [],
    ) {
    }
}
