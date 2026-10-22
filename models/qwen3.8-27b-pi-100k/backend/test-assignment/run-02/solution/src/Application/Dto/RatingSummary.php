<?php

declare(strict_types=1);

namespace App\Application\Dto;

use App\Entity\UsageEvent;

/**
 * Итог app:billing:rate: сколько событий рассчитано в этом запуске и
 * сколько осталось unrated (для stderr-строк).
 *
 * @param list<UsageEvent> $unrated
 */
final readonly class RatingSummary
{
    public function __construct(
        public int $rated,
        public array $unrated,
    ) {
    }
}
