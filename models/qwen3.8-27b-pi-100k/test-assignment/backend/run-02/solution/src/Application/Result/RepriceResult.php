<?php

declare(strict_types=1);

namespace App\Application\Result;

/**
 * Итог ретро-коррекции цен.
 *
 * @param int    $events   число пересчитанных событий
 * @param string $deltaUsd новая сумма минус старая, в формате 6 знаков
 */
final class RepriceResult
{
    public function __construct(
        public readonly int $events,
        public readonly string $deltaUsd,
    ) {
    }
}
