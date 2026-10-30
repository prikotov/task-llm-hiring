<?php

declare(strict_types=1);

namespace App\Ingest\Defs;

/** Провайдер из файла-каталога. */
final class ProviderDef
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
