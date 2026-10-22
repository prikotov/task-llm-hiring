<?php

declare(strict_types=1);

namespace App\Application\Prices;

/**
 * Справочная запись модели из входного файла.
 */
final readonly class ModelSpec
{
    public function __construct(
        public string $provider,
        public string $code,
    ) {
    }
}
