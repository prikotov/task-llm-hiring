<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Money\Decimal;
use InvalidArgumentException;

/**
 * Корректировка уже рассчитанной стоимости события (ретро-коррекция цены).
 * Хранится как журнал: исходная и новая стоимость, причина.
 */
final readonly class CostAdjustment
{
    public function __construct(
        public int $eventId,
        public Decimal $oldCost,
        public Decimal $newCost,
        public string $reason,
        public ?int $id = null,
    ) {
    }
}
