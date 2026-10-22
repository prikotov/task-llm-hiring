<?php

declare(strict_types=1);

namespace App\Domain;

/** Итог одного прогона app:billing:rate. */
final class RatingSummary
{
    /**
     * @param list<UnratedEvent> $unrated
     */
    public function __construct(
        public readonly int $ratedCount,
        public readonly array $unrated,
    ) {
    }

    public function unratedCount(): int
    {
        return \count($this->unrated);
    }
}
