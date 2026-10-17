<?php

declare(strict_types=1);

namespace App\Application\Result;

use App\Entity\UsageEvent;

/**
 * Итог запуска тарификации: сколько событий получило стоимость
 * и сколько осталось unrated именно в этом запуске.
 */
final class RateSummary
{
    /**
     * @param list<UsageEvent> $unratedEvents события, оставшиеся unrated
     */
    public function __construct(
        public readonly int $rated,
        public readonly int $unrated,
        public readonly array $unratedEvents,
    ) {
    }
}
