<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Usage\UsageEvent;

final class RateSummary
{
    /**
     * @param list<UsageEvent> $unratedEvents события ЭТОГО запуска, для которых строка цены не найдена
     */
    public function __construct(
        public readonly int $rated,
        public readonly array $unratedEvents,
    ) {
    }
}
