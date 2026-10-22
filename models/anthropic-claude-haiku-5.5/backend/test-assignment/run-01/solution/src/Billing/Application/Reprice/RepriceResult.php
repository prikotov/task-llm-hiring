<?php

declare(strict_types=1);

namespace App\Billing\Application\Reprice;

final readonly class RepriceResult
{
    /** @param string $deltaUsd Новая минус старая сумма стоимостей пересчитанных событий, 6 знаков. */
    public function __construct(
        public int $repricedEvents,
        public string $deltaUsd,
    ) {
    }
}
