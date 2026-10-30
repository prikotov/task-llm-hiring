<?php

declare(strict_types=1);

namespace App\Ingest\Defs;

/** Модель из файла-каталога. */
final class ModelDef
{
    public function __construct(
        public readonly string $providerCode,
        public readonly string $code,
    ) {
    }
}
