<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * Разобранное и проверенное представление модели из файла цен.
 */
final class ModelData
{
    public function __construct(
        public readonly string $provider,
        public readonly string $code,
    ) {
    }
}
