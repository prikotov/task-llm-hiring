<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Usage\UsageEvent;

/**
 * Сводка одного запуска тарификации.
 *
 * @readonly
 */
final class RateResult
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
