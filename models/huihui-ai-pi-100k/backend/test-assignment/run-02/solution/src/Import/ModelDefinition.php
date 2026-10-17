<?php

declare(strict_types=1);

namespace App\Import;

/**
 * Модель из файла истории цен.
 */
final class ModelDefinition
{
    public function __construct(
        public readonly string $providerCode,
        public readonly string $code,
    ) {
    }
}
