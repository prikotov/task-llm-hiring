<?php

declare(strict_types=1);

namespace App\Billing\Domain\Usage;

use App\Billing\Domain\Model\ModelReference;
use App\Billing\Domain\Money\Money;

/**
 * Кому и за что выставлена стоимость. Нужен для команды ретро-коррекции:
 * после замены строки цен старые значения сохраняются, а не затираются молча.
 */
final readonly class CostAdjustment
{
    public function __construct(
        public ?int $id,
        public int $eventId,
        public Money $previousCost,
        public Money $newCost,
        public ?int $previousPriceRowId,
        public int $newPriceRowId,
        public \DateTimeImmutable $createdAt,
        public string $reason,
    ) {
    }

    public function delta(): Money
    {
        return $this->newCost->subtract($this->previousCost);
    }
}
