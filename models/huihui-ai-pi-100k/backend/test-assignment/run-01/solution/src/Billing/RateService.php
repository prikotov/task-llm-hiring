<?php

declare(strict_types=1);

namespace App\Billing;

use App\Domain\Decimal;
use App\Domain\EventStatus;
use App\Entity\PriceRow;
use App\Entity\UsageEvent;
use App\Util\DateTimeUtc;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация всех ещё не рассчитанных событий (pending и unrated —
 * включая повторные попытки для событий, оставшихся unrated ранее:
 * цены могли подтянуться позже).
 *
 * Стоимость события: точное значение без округления → одно окончательное
 * HALF_UP-округление до 6 знаков (2.5 → 3). Float не используется.
 */
final class RateService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly CostCalculator $calculator,
        private readonly PriceRowSelector $selector,
    ) {
    }

    public function ratePendingEvents(): RateResult
    {
        return $this->em->wrapInTransaction(function (): RateResult {
            $result = new RateResult();

            /** @var list<UsageEvent> $events */
            $events = $this->em->createQuery(
                'SELECT e FROM App\Entity\UsageEvent AS e
                 WHERE e.status = :pending OR e.status = :unrated
                 ORDER BY e.id ASC',
            )
                ->setParameter('pending', EventStatus::Pending)
                ->setParameter('unrated', EventStatus::Unrated)
                ->getResult();

            /** @var array<string, list<UsageEvent>> $byPair */
            $byPair = [];
            foreach ($events as $event) {
                $key = $event->getProvider()->getId() . '|' . $event->getModel()->getId();
                $byPair[$key][] = $event;
            }

            foreach ($byPair as $group) {
                $first = $group[0];
                $provider = $first->getProvider();
                $model = $first->getModel();

                /** @var list<PriceRow> $priceRows */
                $priceRows = $this->em->createQuery(
                    'SELECT r FROM App\Entity\PriceRow AS r
                     WHERE r.provider = :provider AND r.model = :model',
                )
                    ->setParameter('provider', $provider)
                    ->setParameter('model', $model)
                    ->getResult();

                foreach ($group as $event) {
                    $row = $this->selector->select($priceRows, $event->getTs());

                    if ($row === null) {
                        $event->markUnrated();
                        $result->addUnrated(sprintf(
                            'UNRATED %s/%s %s no_price',
                            $provider->getCode(),
                            $event->getProviderRequestId(),
                            DateTimeUtc::format($event->getTs()),
                        ));

                        continue;
                    }

                    $cost = $this->calculator->priceCost($row, $event->getTokenUsage(), $event->getTs(), $provider->offPeak())
                        ->roundHalfUp(6);

                    $event->markRated($row, $cost);
                    $result->ratedCount++;
                }
            }

            return $result;
        });
    }
}
