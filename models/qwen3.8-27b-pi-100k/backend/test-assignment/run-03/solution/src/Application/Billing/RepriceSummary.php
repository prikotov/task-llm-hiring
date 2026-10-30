<?php

declare(strict_types=1);

namespace App\Application\Billing;

final class RepriceSummary
{
    /**
     * @param int    $repricedEvents число пересчитанных событий
     * @param string $deltaUsd       новая минус старая сумма их стоимостей, ровно 6 знаков
     */
    public function __construct(
        public readonly int $repricedEvents,
        public readonly string $deltaUsd,
    ) {
    }
}
