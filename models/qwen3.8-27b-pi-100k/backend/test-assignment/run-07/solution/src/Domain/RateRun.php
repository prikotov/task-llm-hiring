<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Результат прогона app:billing:rate.
 *
 * @readonly
 */
final class RateRun
{
    /**
     * @param list<\App\Entity\UsageEvent> $unrated события, оставшиеся unrated
     */
    public function __construct(
        public readonly int $rated,
        public readonly int $leftUnrated,
        public readonly array $unrated,
    ) {
    }
}
