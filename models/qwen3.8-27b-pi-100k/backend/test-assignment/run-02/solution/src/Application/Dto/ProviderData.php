<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * Разобранное и проверенное представление провайдера из файла цен.
 */
final class ProviderData
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?string $windowStart,
        public readonly ?string $windowEnd,
        public readonly ?string $multiplier,
    ) {
    }
}
