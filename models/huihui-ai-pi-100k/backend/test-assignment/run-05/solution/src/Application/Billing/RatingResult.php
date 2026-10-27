<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Infrastructure\Persistence\Entity\UsageEvent;

/**
 * Итог тарификации (запуск app:billing:rate):
 * количество рассчитанных в этом запуске событий
 * и список оставшихся unrated (для выводов «UNRATED … no_price» в stderr).
 *
 * @readonly
 */
final class RatingResult
{
    /**
     * @param UsageEvent[] $unratedEvents
     */
    public function __construct(
        public readonly int $rated,
        public readonly array $unratedEvents,
    ) {
    }

    public function unratedCount(): int
    {
        return count($this->unratedEvents);
    }
}
