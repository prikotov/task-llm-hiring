<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Persistence\Entity\UsageEvent;

final readonly class RateResult
{
    /**
     * @param list<UsageEvent> $unratedEvents события этого запуска, оставшиеся без строки цен
     */
    public function __construct(
        public int $rated,
        public int $unrated,
        public array $unratedEvents,
    ) {
    }
}
