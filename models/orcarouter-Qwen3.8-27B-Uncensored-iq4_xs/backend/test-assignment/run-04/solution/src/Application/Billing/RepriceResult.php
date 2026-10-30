<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Money\Money;

/**
 * Результат ретро-коррекции: число пересчитанных событий
 * и дельта стоимости (новая сумма − старая сумма), 6 знаков.
 */
final class RepriceResult
{
    public function __construct(
        public readonly int $repricedEvents,
        public readonly Money $delta,
    ) {
    }

    public function deltaFixed6(): string
    {
        return $this->delta->asFixed6String();
    }
}
