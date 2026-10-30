<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Catalog\PriceLineRepository;
use App\Domain\Usage\UsageEvent;
use App\Domain\Usage\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий (включая повторные попытки
 * предыдущих unrated — цены могли подтянуться позже).
 */
final class BillingService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
        private readonly PriceLineRepository $priceLines,
        private readonly RateCalculator $calculator,
    ) {
    }

    /**
     * @return array{rated:int, unrated:list<UsageEvent>} результаты этого запуска
     */
    public function ratePendingEvents(): array
    {
        $rated = 0;
        $unrated = [];

        $this->em->wrapInTransaction(function () use (&$rated, &$unrated): void {
            foreach ($this->events->findPending() as $event) {
                $line = $this->priceLines->findApplicableLine(
                    $event->provider(),
                    $event->model(),
                    $event->ts(),
                );

                if ($line === null) {
                    $unrated[] = $event;
                    continue;
                }

                $provider = $line->provider();
                $event->rate($this->calculator->eventCost($event, $line, $provider), $line);
                $rated++;
            }

            $this->em->flush();
        });

        return ['rated' => $rated, 'unrated' => $unrated];
    }
}
