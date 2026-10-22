<?php

declare(strict_types=1);

namespace App\Application\Billing;

use DateTimeImmutable;

final readonly class UnratedEvent
{
    public function __construct(
        public string $provider,
        public string $providerRequestId,
        public DateTimeImmutable $occurredAt,
    ) {
    }
}
