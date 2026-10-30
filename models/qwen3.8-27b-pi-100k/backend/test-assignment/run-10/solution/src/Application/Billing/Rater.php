<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Application\Import\CatalogRepository;
use App\Domain\Billing\EventPricingService;
use App\Entity\Price;
use App\Entity\UsageEvent;
use App\Entity\UsageRating;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Низкоуровневый «растер»: тарификация, пересчёт по рецензии,
 * выбор «последнего действующего» rating'а для события.
 *
 * Не содержит бизнес-логики «что и за сколько»: это механика записей
 * usage_ratings (create / lookup / reprice).
 */
final class Rater
{
    /** @var \DateTimeImmutable|null */
    private ?\DateTimeImmutable $repriceAt = null;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventPricingService $eventPricing,
        private readonly CatalogRepository $catalog,
    ) {
    }

    public function loadEvents(array $events, bool $withRatings = false): void
    {
        foreach ($events as $e) {
            $this->em->refresh($e, ['model', 'provider']);
            if ($withRatings) {
                $this->em->refresh($e);
                $this->catalog->pricesForModel($e->getModel());
            }
        }
    }

    public function loadAffectedRatings(array $affectedPrices): void
    {
        foreach ($affectedPrices as $p) {
            $this->em->refresh($p);
        }
    }

    public function latestRatingFor(UsageEvent $event): ?UsageRating
    {
        $best = null;
        foreach ($event->getRatings() as $r) {
            if ($best === null || $r->getEffectiveFrom() > $best->getEffectiveFrom() || ($r->getEffectiveFrom() == $best->getEffectiveFrom() && $r->getId() > $best->getId())) {
                $best = $r;
            }
        }

        return $best;
    }

    /**
     * @param list<array{provider: string, providerRequestId: string, ts: string}> $unrated
     */
    public function rateSingleEvent(UsageEvent $event, array &$stats): void
    {
        $r = $this->eventPricing->rateEvent($event);

        if ($r === null) {
            $this->em->persist(new UsageRating($event, $this->now(), null, null, null));
            $event->setStatus(UsageEvent::STATUS_UNRATED);
            $stats[1]++;
            $stats[2][] = [
                'provider' => $event->getProvider()->getCode(),
                'providerRequestId' => $event->getProviderRequestId(),
                'ts' => $event->getTs()->format(\DateTimeInterface::ATOM),
            ];

            return;
        }

        $cost = $r['cost']->roundHalfUp6()->toFixed(6);
        $this->em->persist(new UsageRating($event, $this->now(), $cost, $r['price'], null));
        $event->setStatus(UsageEvent::STATUS_RATED);
        $stats[0]++;
    }

    /**
     * @param list<Price> $affected
     * @param int $repriced счётчик
     * @param \App\Domain\Billing\Decimal $delta накопление
     */
    public function reprice(array $affected, int &$repriced, \App\Domain\Billing\Decimal &$delta): void
    {
        $this->repriceAt = (new \DateTimeImmutable('now'))->setTimezone(new \DateTimeZone('UTC'));
        $affectedIds = array_map(static fn (Price $p) => $p->getId(), $affected);

        $events = $this->em->getRepository(UsageEvent::class)->createQueryBuilder('e')
            ->join('e.ratings', 'r')
            ->andWhere('r.price IN (:ids)')->setParameter('ids', $affectedIds)
            ->andWhere('e.status = :st')->setParameter('st', UsageEvent::STATUS_RATED)
            ->getQuery()
            ->getResult();

        foreach ($events as $event) {
            $r = $this->eventPricing->rateEvent($event);
            if ($r === null) {
                continue;
            }
            $newCost = $r['cost']->roundHalfUp6()->toFixed(6);
            $oldRating = $this->latestRatingFor($event);
            if ($oldRating === null || $oldRating->getCost() === null) {
                continue;
            }
            $oldCost = $oldRating->getCost();

            $delta = \App\Domain\Billing\Decimal::fromString($newCost)->subtract(\App\Domain\Billing\Decimal::fromString($oldCost));

            $this->em->persist(new UsageRating($event, $this->repriceAt, $newCost, $r['price'], $this->repriceAt));
            $repriced++;
        }
    }

    private function now(): \DateTimeImmutable
    {
        $now = new \DateTimeImmutable('now');

        return $now->setTimezone(new \DateTimeZone('UTC'));
    }

    /**
     * @return array{0: int, 1: int, 2: list<array{provider: string, providerRequestId: string, ts: string}>}
     */
    public function stats(): array
    {
        throw new \LogicException('use rateAllPending result');
    }
}
