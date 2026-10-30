<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money;
use App\Entity\Price;
use App\Entity\UsageEvent;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус TASK.md).
 *
 * Применяет исправленные строки цен и пересчитывает именно те события,
 * которые были тарифицированы этими строками; уже показанные расходы
 * не дублируются (стоимость события перезаписывается, а не доначисляется).
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RatingService $rating,
        private readonly PriceCatalogService $catalog,
        private readonly UsageEventRepository $events,
    ) {
    }

    /**
     * @param array<int, array<string, mixed>> $priceRows секция prices из файла коррекции
     * @return array{events: int, deltaUsd: string}
     */
    public function reprice(array $priceRows): array
    {
        /** @var Price[] $touched */
        $touched = $this->catalog->upsertPriceRowsOnly($priceRows);

        $totalEvents = 0;
        $oldSum = '0';
        $newSum = '0';

        foreach ($touched as $price) {
            $providerCode = $price->getProvider()->getCode();
            $modelCode = $price->getModel()->getCode();
            $validFrom = $price->getValidFrom();

            /** @var UsageEvent[] $affected */
            $affected = $this->events->findRatedByPriceRow($providerCode, $modelCode, $validFrom);

            foreach ($affected as $event) {
                $oldCost = $event->getCost() ?? '0';
                $newCost = $this->rating->recalculate($event, $price);

                $event->markRated($newCost, $price->getCurrency(), $validFrom);

                $oldSum = Money::add($oldSum, $oldCost);
                $newSum = Money::add($newSum, $newCost);
                ++$totalEvents;
            }
        }

        $this->em->flush();

        $delta = Money::format(Money::sub($newSum, $oldSum), 6);

        return ['events' => $totalEvents, 'deltaUsd' => $delta];
    }
}
