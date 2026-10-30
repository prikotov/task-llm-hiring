<?php

declare(strict_types=1);

namespace App\Application\Billing;

/**
 * Итог ретро-коррекции цен (app:billing:reprice).
 *
 * @readonly
 */
final class RepriceResult
{
    public function __construct(
        /** Количество пересчитанных событий. */
        public readonly int $repricedEvents,
        /** Новая сумма минус старая, бцшмм, ровно 6 знаков (знак может быть «-»). */
        public readonly string $deltaUsd,
    ) {
    }
}
