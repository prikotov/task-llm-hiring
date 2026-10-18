<?php

declare(strict_types=1);

namespace App\Import;

/** Строка секции "models" файла цен. */
final readonly class ModelDoc
{
    public function __construct(
        public string $provider,
        public string $code,
    ) {
    }
}
