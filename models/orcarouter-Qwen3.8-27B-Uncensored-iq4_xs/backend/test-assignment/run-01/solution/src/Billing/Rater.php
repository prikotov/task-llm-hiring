<?php

declare(strict_types=1);

namespace App\Billing;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация всех ещё не рассчитанных событий (включая повторные
 * попытки для unrated — они остаются в статусе pending).
 */
final class Rater
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @return array{rated: int, unrated: list<UsageEvent>}
     */
    public function ratePending(): array
    {
        $em = $this->em;

        return $em->wrapInTransaction(function (EntityManagerInterface $em) {
            /** @var UsageEvent[] $events */
            $events = $em->getRepository(UsageEvent::class)->findBy(['status' => UsageEvent::STATUS_PENDING]);

            $book = new PriceBook($em);
            $unrated = [];

            foreach ($events as $event) {
                $rate = $book->findRate($event->getProviderCode(), $event->getModelCode(), $event->getTs());
                if ($rate === null) {
                    $unrated[] = $event;
                    continue;
                }

                $cost = CostCalculator::computeEventCost(
                    $event->tokens(),
                    $rate['row'],
                    $rate['window'],
                    $event->getTs(),
                );
                $event->markRated($rate['row'], $cost);
            }

            $em->flush();

            return ['rated' => \count($events) - \count($unrated), 'unrated' => $unrated];
        });
    }
}
