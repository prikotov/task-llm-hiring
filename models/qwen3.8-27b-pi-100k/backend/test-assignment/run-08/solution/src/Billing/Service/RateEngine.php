<?php

declare(strict_types=1);

namespace App\Billing\Service;

use App\Billing\Billing;
use App\Billing\Entity\PriceRow;
use App\Billing\Entity\Provider;
use App\Billing\Entity\UsageEvent;
use App\Billing\Pricing\RateCalculator;
use App\Billing\Repository\PriceRowRepository;
use App\Billing\Repository\UsageEventRepository;
use App\Billing\ValueObject\Decimal;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий (включая повторные попытки unrated).
 *
 * Правила (TASK.md):
 *  1) цена выбирается строкой модели с наибольшим valid_from ≤ ts;
 *  3) оф-пик провайдера — множитель стоимости всего события;
 *  4) единственное округление — итога события, HALF_UP до 6 знаков;
 *  7) unrated остаётся pending и повторно рассматривается на каждом rate.
 */
final class RateEngine
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RateCalculator $calculator,
        private readonly PriceRowRepository $priceRows,
        private readonly UsageEventRepository $events,
    ) {
    }

    public function ratePending(): RateRunResult
    {
        return $this->em->wrapInTransaction(function (): RateRunResult {
            /** @var list<UsageEvent> $pending */
            $pending = $this->events->findAllPending();

            $rated = 0;
            $unrated = [];

            foreach ($pending as $event) {
                $priceRow = $this->priceRows->findApplicable($event->provider, $event->model, $event->ts);
                if ($priceRow === null) {
                    $unrated[] = $event;
                    continue;
                }

                $event->markRated($this->eventCost($event, $priceRow), $priceRow->id, new \DateTimeImmutable());
                $rated++;
            }

            $this->em->flush();

            return new RateRunResult($rated, $unrated);
        });
    }

    /**
     * Точная стоимость события: база по схеме цены × возможный оф-пиковый множитель,
     * затем единственное округление HALF_UP до 6 знаков.
     */
    private function eventCost(UsageEvent $event, PriceRow $priceRow): Decimal
    {
        $provider = $this->em->find(Provider::class, $event->provider);
        $offPeak = $provider?->offPeak();
        $multiplier = $offPeak !== null && $offPeak->isOffPeak($event->ts) ? $offPeak->multiplier : null;

        return $this->calculator
            ->cost($priceRow->price(), $event->usage(), $multiplier)
            ->round(Billing::COST_DECIMALS);
    }
}
