<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Catalog\Provider;
use App\Domain\Usage\EventPricingCalculator;
use App\Domain\Usage\UsageEvent;
use App\Infrastructure\Persistence\PriceRowRepository;
use App\Infrastructure\Persistence\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий (включая повторные попытки
 * для unrated — цены могли подтянуться позже).
 */
final class EventRater
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
        private readonly PriceRowRepository $priceRows,
        private readonly EventPricingCalculator $calculator,
    ) {
    }

    public function ratePending(): RatingResult
    {
        $pending = $this->events->findUnrated();
        $rated = 0;
        $unrated = [];
        $providers = [];

        foreach ($pending as $event) {
            $price = $this->priceRows->findEffectiveForModel(
                $event->provider(),
                $event->model()->code(),
                $event->ts(),
            );
            if ($price === null) {
                $unrated[] = $event;

                continue;
            }

            $provider = $providers[$event->provider()] ??= $this->em->find(Provider::class, $event->provider());
            $cost = $this->calculator->computeEventCost($event, $price, $provider?->offPeak());
            $event->rateWith($price, $cost);
            $rated++;
        }

        $this->em->flush();

        return new RatingResult($rated, $unrated);
    }
}
