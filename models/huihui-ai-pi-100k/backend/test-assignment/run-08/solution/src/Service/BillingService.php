<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Billing\OffPeak;
use App\Domain\Billing\PricingCalculator;
use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Repository\PriceRepository;
use App\Repository\UsageEventRepository;

/**
 * Тарификация ещё не расценённых событий.
 *
 * Правила:
 *  - выбор строки цены: наибольшее valid_from ≤ ts (включительно)
 *    для пары (provider, model);
 *  - стоимость события считается в точной десятичной арифметике,
 *    оф-пиковый множитель применяется, если ts (UTC) попал в окно,
 *    итог события — ровно одно округление HALF_UP до 6 знаков;
 *  - событие без подходящей строки цены остаётся unrated
 *    (повторные попытки при каждом запуске);
 *  - идемпотентность: уже расценённые события не пересчитываются.
 */
final class BillingService
{
    public function __construct(
        private readonly \Doctrine\ORM\EntityManagerInterface $em,
        private readonly PriceRepository $priceRepository,
        private readonly UsageEventRepository $eventRepository,
    ) {
    }

    /**
     * @return array{rated:int, unrated:int, unratedEvents:list<array{provider:string, requestId:string, ts:string}>}
     */
    public function rateAllPending(): array
    {
        $em = $this->em;

        return $em->wrapInTransaction(function () use ($em): array {
            $pending = $em->getRepository(UsageEvent::class)
                ->createQueryBuilder('e')
                ->where('e.status = :status')
                ->setParameter('status', UsageEvent::STATUS_PENDING)
                ->getQuery()
                ->getResult();

            $rated = 0;
            $unrated = 0;
            $unratedEvents = [];

            foreach ($pending as $event) {
                if (! $event instanceof UsageEvent) {
                    continue;
                }

                $price = $this->priceRepository->findPriceFor($event);
                if ($price === null) {
                    $unrated++;
                    $unratedEvents[] = [
                        'provider' => $event->providerCode,
                        'requestId' => $event->providerRequestId,
                        'ts' => $event->ts,
                    ];
                    continue;
                }

                $offPeak = $this->providerOffPeak($event->providerCode);
                $cost = PricingCalculator::computeEventCost(
                    $event->tokenCounts(),
                    $price->unit,
                    $price->rates,
                    $offPeak,
                    new \DateTimeImmutable($event->ts),
                );

                $event->markRated($price->validFrom, $cost);
                $rated++;
            }

            $em->flush();

            return ['rated' => $rated, 'unrated' => $unrated, 'unratedEvents' => $unratedEvents];
        });
    }

    private function providerOffPeak(string $code): ?OffPeak
    {
        $provider = $this->em->getRepository(Provider::class)->find($code);

        return $provider instanceof Provider ? $provider->offPeak() : null;
    }
}
