<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Естественный ключ события: (provider, providerRequestId).
 */
final class EventKey
{
    public function __construct(
        public readonly string $provider,
        public readonly string $providerRequestId,
    ) {
    }
}
