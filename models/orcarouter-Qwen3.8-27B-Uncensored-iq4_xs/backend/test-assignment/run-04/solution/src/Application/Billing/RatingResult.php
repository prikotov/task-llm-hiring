<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Usage\UsageEvent;

/**
 * Результат запуска тарификации: сколько событий рассчитано в этом запуске
 * и какие остались unrated (повторно попытаются в следующем запуске).
 */
final class RatingResult
{
    /**
     * @param UsageEvent[] $unrated
     */
    public function __construct(
        public readonly int $ratedCount,
        public readonly array $unrated,
    ) {
    }
}
