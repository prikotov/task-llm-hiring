<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Money\Cost;
use App\Domain\Model\Model;
use App\Domain\Price\PriceRow;
use App\Domain\Price\RateUnit;
use Doctrine\ORM\EntityManager;

/**
 * Тарификация «ещё не рассчитанных» событий; идемпотентна.
 *
 * Пункт 1: строка цен — наибольшее valid_from <= ts события
 * (valid_from включительно). Если строки нет — unrated (оставляется unrated, stderr).
 * Пункт 4: без промежуточного округления, округление события — 6 знаков HALF_UP.
 * Пункт 5: идемпотентность на уровне БД — event.priceRow не null => уже тарифицирован.
 * Пункт 7: unrated повторяют попытку в каждом запуске (цены могли подтянуться).
 */
final class RateUnratedUsageEvents
{
    public function __construct(
        private EntityManager $em,
    ) {}

    /**
     * @throws \Exception
     *
     * @return list<array{0: int 1: string 2: string}> (id, provider/request_id, iso ts) для unrated
     */
    public function rate(): array
    {
        $unrated = [];
        $events = $this->em->getRepository(UsageEvent::class)->findBy([
            'priceRow' => null,
            'costUsd' => null,
        ], ['id' => 'ASC']);

        $calculator = new EventCostCalculator();
        $unpeaked = [];

        foreach ($events as $event) {
            /** @var PriceRow|null $row */
            $row = $this->pickApplicablePrice($event);
            if ($row === null) {
                $unrated[] = [$event->id(), $event->provider()->code().'/'.$event->providerRequestId(), $event->ts()->format('Y-m-d\TH:i:s\Z')];
                continue;
            }

            $offPeak = $event->provider()->offPeakSettings();
            $rates = $row->rates();
            $cost = $calculator->calculate($event, $rates, $row->unit(), $offPeak);

            $event->assignRate($row, $cost->roundForEvent());
        }

        $this->em->flush();

        return $unrated;
    }

    /**
     * Пункт 1: строка цен — наибольшее valid_from <= ts.
     */
    private function pickApplicablePrice(UsageEvent $event): ?PriceRow
    {
        $model = $event->model();
        $provider = $event->provider();

        $q = $this->em->createQueryBuilder()
            ->select('p')
            ->from(PriceRow::class, 'p')
            ->where('p.provider = :prov')
            ->andWhere('p.model = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('prov', $provider)
            ->setParameter('model', $model)
            ->setParameter('ts', $event->ts())
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $result = $q->getOneOrNullResult();

        /** @var PriceRow|null $result */

        return $result;
    }
}
