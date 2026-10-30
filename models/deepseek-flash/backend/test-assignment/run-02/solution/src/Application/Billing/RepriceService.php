<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Application\Money\Money;
use App\Domain\Pricing\Entity\Price;
use App\Domain\Pricing\Entity\ProviderModel;
use App\Domain\Pricing\Service\PricingCalculator;
use App\Domain\Usage\Entity\UsageEvent;
use App\Domain\Usage\Entity\UsageEventReprice;
use App\Domain\Usage\ValueObject\RateStatus;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;

final readonly class RepriceService
{
    public function __construct(
        private EntityManagerInterface $em,
        private PricingCalculator $calculator,
    ) {
    }

    /**
     * @param list<Price> $changedPrices строки цен, изменённые коррекцией
     */
    public function reprice(array $changedPrices): RepriceResult
    {
        if ($changedPrices === []) {
            return new RepriceResult(0, '0.000000');
        }

        $changedPriceIds = [];
        $models = [];
        foreach ($changedPrices as $price) {
            $changedPriceIds[$price->id()] = true;
            $model = $price->providerModel();
            $models[spl_object_id($model)] = $model;
        }

        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));
        $eventRepository = $this->em->getRepository(UsageEvent::class);
        $repriced = 0;
        $delta = '0.000000';

        foreach ($models as $model) {
            /** @var list<UsageEvent> $events */
            $events = $eventRepository->createQueryBuilder('e')
                ->where('e.status = :status')
                ->andWhere('e.providerCode = :provider')
                ->andWhere('e.modelCode = :model')
                ->setParameter('status', RateStatus::Rated)
                ->setParameter('provider', $model->provider()->code())
                ->setParameter('model', $model->code())
                ->orderBy('e.id', 'ASC')
                ->getQuery()
                ->getResult();

            foreach ($events as $event) {
                $oldPrice = $event->price();
                $oldCost = $event->costUsd();
                $newPrice = $this->findPrice($model, $event->occurredAt());

                if ($newPrice === null) {
                    continue;
                }

                $newCost = $this->calculator->calculate($event, $newPrice);

                $oldPriceChanged = $oldPrice !== null && isset($changedPriceIds[$oldPrice->id()]);
                $newPriceChanged = isset($changedPriceIds[$newPrice->id()]);
                $priceChanged = $oldPrice?->id() !== $newPrice->id();

                if (!$oldPriceChanged && !$newPriceChanged && !$priceChanged) {
                    continue;
                }

                $this->em->persist(new UsageEventReprice(
                    $event,
                    $oldPrice,
                    $newPrice,
                    $oldCost,
                    $newCost,
                    RateStatus::Rated,
                    RateStatus::Rated,
                    $now,
                ));

                $event->markRated($newPrice, $newCost, $now);
                $delta = Money::add($delta, Money::sub($newCost, (string) $oldCost), Money::MONEY_SCALE);
                ++$repriced;
            }
        }

        $this->em->flush();

        return new RepriceResult($repriced, Money::format($delta));
    }

    private function findPrice(ProviderModel $model, DateTimeImmutable $moment): ?Price
    {
        /** @var Price|null $price */
        $price = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Price::class, 'p')
            ->where('p.providerModel = :model')
            ->andWhere('p.validFrom <= :moment')
            ->setParameter('model', $model)
            ->setParameter('moment', $moment)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();

        return $price;
    }
}
