<?php

declare(strict_types=1);

namespace App\Billing;

/**
 * Результат однго прогона app:billing:rate.
 */
final class RateRunResult
{
    /**
     * @param list<array{provider: string, providerRequestId: string, ts: \DateTimeImmutable}> $unrated
     */
    public function __construct(
        public readonly int $rated,
        public readonly array $unrated,
    ) {
    }

    public function unratedCount(): int
    {
        return \count($this->unrated);
    }
}
