<?php

declare(strict_types=1);

namespace App\Application\Billing;

final readonly class RatingResult
{
    /**
     * @param list<UnratedEvent> $unratedEvents
     */
    public function __construct(
        public int $rated,
        public int $unrated,
        public array $unratedEvents,
    ) {
    }
}
