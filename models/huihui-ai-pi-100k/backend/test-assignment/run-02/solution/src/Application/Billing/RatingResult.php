<?php

declare(strict_types=1);

namespace App\Application\Billing;

use DateTimeImmutable;

/**
 * Итог запуска `app:billing:rate` (только текущий запуск).
 *
 * @psalm-suppress UnusedProperty
 */
final readonly class RatingResult
{
    /**
     * @param UnratedEvent[] $unrated
     */
    public function __construct(
        public int $ratedCount,
        public array $unrated,
    ) {
    }
}
