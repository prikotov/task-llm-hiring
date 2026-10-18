<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Dto\RatingSummary;
use App\Billing\OffPeak\OffPeakPricing;
use App\Billing\Pricing\PricingCalculator;
use App\Entity\Model;
use App\Entity\PriceRow;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * app:billing:rate — тарификация всех ещё не рассчитанных событий.
 *
 * Повторяет попытку и для событий, оставшихся unrated: строка цены могла
 * поступить позже. Rated-события не пересчитываются (кроме reprice).
 *
 * Выбор цены: строка модели с наибольшим valid_from ≤ ts (включительно);
 * цены, вступившие в силу после события, на него не влияют.
 */
final class RateService
{
    /**
     * Кеш «(провайдер, минуты суток) → множитель оф-пика» на время одного
     * запуска (множитель зависит и от времени события, а не только от провайдера).
     *
     * @var array<string, ?string>
     */
    private array $offPeakMultiplierCache = [];

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PricingCalculator $calculator = new PricingCalculator(),
    ) {
    }

    public function rate(): RatingSummary
    {
        return $this->em->getConnection()->transactional(function (): RatingSummary {
            /** @var UsageEvent[] $pending */
            $pending = $this->em
                ->createQuery(
                    'SELECT e FROM '.UsageEvent::class.' e '.
                    'WHERE e.status IN (:statuses) '
                    .'ORDER BY e.id ASC',
                )
                ->setParameter('statuses', [UsageEvent::STATUS_NEW, UsageEvent::STATUS_UNRATED])
                ->getResult();

            $rated = 0;
            $unrated = [];
            $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

            foreach ($pending as $event) {
                $priceRow = $this->findEffectivePriceRow($event->getModel(), $event->getTs());
                if ($priceRow === null) {
                    $event->markUnrated();
                    $unrated[] = $event;
                    continue;
                }

                $cost = $this->calculator->calculateEventCost(
                    $priceRow->unitAsEnum(),
                    $event->tokens(),
                    $priceRow->getRates(),
                    $this->offPeakMultiplierFor($event),
                );
                $event->markRated($priceRow, $cost, $now);
                ++$rated;
            }

            $this->em->flush();

            return new RatingSummary($rated, $unrated);
        });
    }

    /** Наибольшее valid_from ≤ ts (включительно) для модели. */
    private function findEffectivePriceRow(Model $model, \DateTimeImmutable $ts): ?PriceRow
    {
        /** @var PriceRow|null $row */
        $row = $this->em
            ->createQuery(
                'SELECT p FROM '.PriceRow::class.' p '.
                'WHERE p.model = :model AND p.validFrom <= :ts '.
                'ORDER BY p.validFrom DESC',
            )
            ->setParameter('model', $model)
            ->setParameter('ts', $ts)
            ->setMaxResults(1)
            ->getOneOrNullResult();

        return $row;
    }

    /** Множитель, если время события (UTC) в окне оф-пика провайдера; иначе null. */
    private function offPeakMultiplierFor(UsageEvent $event): ?string
    {
        $provider = $event->getProvider();
        $minutesOfDay = (int) $event->getTs()->format('G') * 60 + (int) $event->getTs()->format('i');
        $cacheKey = $provider->getId().':'.$minutesOfDay;

        if (!array_key_exists($cacheKey, $this->offPeakMultiplierCache)) {
            $this->offPeakMultiplierCache[$cacheKey] = OffPeakPricing::multiplierAt(
                $provider->getOffPeakWindowStart(),
                $provider->getOffPeakWindowEnd(),
                $provider->getOffPeakMultiplier(),
                $minutesOfDay,
            );
        }

        return $this->offPeakMultiplierCache[$cacheKey];
    }
}
