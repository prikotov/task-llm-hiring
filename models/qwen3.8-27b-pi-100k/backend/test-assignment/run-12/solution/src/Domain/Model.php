<?php

declare(strict_types=1);

namespace App\Domain;

/** Модель провайдера. Код уникален среди моделей одного провайдера. */
final class Model
{
    public function __construct(
        public readonly string $provider,
        public readonly string $code,
    ) {
    }

    public function qualifiedName(): string
    {
        return sprintf('%s/%s', $this->provider, $this->code);
    }
}
