<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Decimal;
use App\Infrastructure\RateCalculator;
use App\Entity\Price;
use App\Entity\UsageEvent;
use App\Repository\PriceRepository;

/**
 * Тарификация события использования.
 *
 * Делегирует выбор строки цены PriceRepository, а расчёт стоимости в
 * пределах выбранной строки (схемы тарификации и оф-пиковая скидка) —
 * RateCalculator, который округляет стоимость до 6 знаков HALF_UP.
 *
 * unrated-событие (без подходящей строки цены) — null.
 */
final class Billing
{
    private readonly RateCalculator $rateCalculator;

    public function __construct(
        private readonly PriceRepository $priceRepository
    ) {
        $this->rateCalculator = new RateCalculator(
            fn (UsageEvent $event): null|Price => $this->priceRepository->priceFor($event)
        );
    }

    /**
     * Стоимость одного события (на дату вступления в силу цены).
     * null — если под момент события нет строки цены.
     */
    public function rate(UsageEvent $event): ?Decimal
    {
        $price = $this->priceRepository->priceFor($event);
        if ($price === null) {
            return null;
        }

        return $this->rateCalculator->rateFor($price, $event);
    }
}
