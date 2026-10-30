<?php

declare(strict_types=1);

namespace App\Application\Import;

/**
 * Запрошенный провайдер из файла справочника.
 */
final class ProviderRecord
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?string $offPeakStart,
        public readonly ?string $offPeakEnd,
        public readonly ?string $offPeakMultiplier,
    ) {
    }
}
