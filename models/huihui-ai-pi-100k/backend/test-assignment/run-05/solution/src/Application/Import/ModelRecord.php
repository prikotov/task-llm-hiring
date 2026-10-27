<?php

declare(strict_types=1);

namespace App\Application\Import;

/**
 * Запрошенная модель из файла справочника.
 */
final class ModelRecord
{
    public function __construct(
        public readonly string $provider,
        public readonly string $code,
    ) {
    }
}
