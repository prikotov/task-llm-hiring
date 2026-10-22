<?php

declare(strict_types=1);

namespace App\Billing\Domain\Usage;

use App\Billing\Domain\Model\OffPeakWindow;
use App\Billing\Domain\Money\Money;

/**
 * Результат тарификации события: сколько и по какой строке цен.
 * Стоимость уже округлена до 6 знаков — это единственное округление (TASK п. 4).
 *
 * priceRowId нужен для ретро-коррекции: по нему вычисляются события,
 * которые надо пересчитать после замены строки цен.
 */
final readonly class Rating
{
    public function __construct(
        public Money $cost,
        public ?int $priceRowId,
        public \DateTimeImmutable $ratedAt,
        public ?OffPeakWindow $appliedOffPeak = null,
    ) {
    }

    public function withCost(Money $cost, \DateTimeImmutable $at): self
    {
        return new self($cost, $this->priceRowId, $at, $this->appliedOffPeak);
    }
}
