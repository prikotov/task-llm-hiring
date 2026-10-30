<?php

declare(strict_types=1);

namespace App\Application\Pricing;

final readonly class ModelSpec
{
    public function __construct(
        /** Код провайдера, которому принадлежит модель. */
        public string $providerCode,
        public string $code,
    ) {
    }
}
