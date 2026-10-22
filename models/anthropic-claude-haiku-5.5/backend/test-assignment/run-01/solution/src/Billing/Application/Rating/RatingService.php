<?php

declare(strict_types=1);

namespace App\Billing\Application\Rating;

use App\Billing\Domain\Catalog\Model;
use App\Billing\Domain\Usage\UsageEvent;
use App\Billing\Infrastructure\Persistence\ModelRepository;
use App\Billing\Infrastructure\Persistence\PriceRepository;
use App\Billing\Infrastructure\Persistence\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/** Тарификация всех ещё не рассчитанных событий. */
final class RatingService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
        private readonly ModelRepository $models,
        private readonly PriceRepository $prices,
    ) {
    }

    /**
     * @return array{rated: int, unrated: list<UsageEvent>}
     */
    public function ratePending(): array
    {
        $rated = 0;
        $unrated = [];

        /** @var array<string, ?Model> $modelCache */
        $modelCache = [];

        foreach ($this->events->findUnrated() as $event) {
            $cacheKey = $event->provider().'/'.$event->model();
            if (!array_key_exists($cacheKey, $modelCache)) {
                $modelCache[$cacheKey] = $this->models->findOneByKey($event->provider(), $event->model());
            }
            $model = $modelCache[$cacheKey];

            $price = $model === null ? null : $this->prices->findActiveFor($model, $event->ts());
            if ($price === null) {
                $unrated[] = $event;
                continue;
            }

            $event->rateWith($price);
            ++$rated;
        }

        $this->em->flush();

        return ['rated' => $rated, 'unrated' => $unrated];
    }
}
