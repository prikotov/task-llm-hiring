<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Usage\Entity\UsageEvent;

final readonly class RateResult
{
    /**
     * @param list<UsageEvent> $unratedEvents
     */
    public function __construct(
        public int $rated,
        public array $unratedEvents,
    ) {
    }

    public function unrated(): int
    {
        return count($this->unratedEvents);
    }
}
