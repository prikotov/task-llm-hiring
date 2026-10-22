<?php

declare(strict_types=1);

namespace App\Billing;

use App\Domain\Usage\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация событий: каждый запуск повторяет попытку для всех ещё не
 * рассчитанных (в т.ч. ранее unrated) событий. Рассчитанные события больше
 * не обрабатываются («повторные попытки» касаются только rated = false).
 */
final class RaterService
{
    private const FLUSH_BATCH = 500;

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceCatalog $catalog,
        private readonly CostCalculator $calculator,
    ) {
    }

    public function ratePending(): RateRunResult
    {
        $unrated = [];
        $rated = 0;

        $this->em->wrapInTransaction(function () use (&$unrated, &$rated): void {
            /** @var UsageEvent[] $events */
            $events = $this->em->getRepository(UsageEvent::class)->findBy(['rated' => false], ['id' => 'ASC']);

            foreach ($events as $event) {
                $price = $this->catalog->findPrice($event->provider(), $event->model(), $event->ts());
                if ($price === null) {
                    $unrated[] = [
                        'provider' => $event->provider(),
                        'providerRequestId' => $event->providerRequestId(),
                        'ts' => $event->ts(),
                    ];
                    continue;
                }

                $cost = $this->calculator->compute(
                    $price,
                    $event->tokens(),
                    $this->catalog->offPeak($event->provider()),
                    $event->ts(),
                );

                $event->markRated($cost->format6(), $price->validFrom());
                $rated++;

                if ($rated % self::FLUSH_BATCH === 0) {
                    $this->em->flush();
                }
            }
        });

        return new RateRunResult($rated, $unrated);
    }
}
