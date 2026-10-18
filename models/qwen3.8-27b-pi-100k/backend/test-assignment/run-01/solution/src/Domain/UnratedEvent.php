<?php

declare(strict_types=1);

namespace App\Domain;

use DateTimeImmutable;

/** Событие, не получившее цены в текущем прогоне тарификации. */
final class UnratedEvent
{
    public function __construct(
        public readonly string $provider,
        public readonly string $requestId,
        public readonly DateTimeImmutable $ts,
    ) {
    }
}
