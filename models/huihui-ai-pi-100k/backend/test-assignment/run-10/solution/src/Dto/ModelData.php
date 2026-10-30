<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * Модель из файла цен.
 */
final class ModelData
{
    public function __construct(
        public readonly string $provider,
        public readonly string $code,
    ) {
    }
}
