<?php

declare(strict_types=1);

namespace App\Application\Import;

final readonly class ModelDefinition
{
    public function __construct(
        public string $providerCode,
        public string $code,
    ) {
    }
}
