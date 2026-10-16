<?php

declare(strict_types=1);

namespace App\Ingest;

/**
 * Ссылка на модель (code в пределах провайдера).
 *
 * @readonly
 */
final class ModelRef
{
    public function __construct(
        public readonly string $provider,
        public readonly string $code,
    ) {
    }
}
