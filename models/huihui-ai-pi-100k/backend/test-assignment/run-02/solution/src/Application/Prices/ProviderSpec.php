<?php

declare(strict_types=1);

namespace App\Application\Prices;

/**
 * Справочная запись провайдера из входного файла.
 */
final readonly class ProviderSpec
{
    public function __construct(
        public string $code,
        public string $name,
        public ?OffPeakSpec $offPeak,
    ) {
    }
}
