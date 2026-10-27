<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

/**
 * Модель провайдера из файла истории цен.
 *
 * @readonly
 */
final class ModelSpec
{
    public function __construct(
        public readonly string $provider,
        public readonly string $code,
    ) {
    }
}
