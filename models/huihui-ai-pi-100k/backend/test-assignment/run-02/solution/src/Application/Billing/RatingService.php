<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Billing\RateCalculator;
use App\Domain\Shared\TimeOfDay;
use App\Domain\Usage\UsageEvent;
use App\Domain\Usage\UsageStatus;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация событий (p. 1-4, 7 задания):
 *
 *  - выбирают строку цен модели с наибольшим `valid_from <= ts` события
 *    (`valid_from` включительно); нет строки — событие unrated;
 *  - стоимость считается точно (bcmath, масштаб 32) и ровно один раз
 *    округляется до 6 знаков HALF_UP;
 *  - оф-пик провайдера применяется по UTC-времени суток `ts`;
 *  - каждый запуск повторяет попытку для всех ещё unrated-событий
 *    (цены могли подтянуться позже).
 */
final class RatingService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly RateCalculator $calculator,
    ) {
    }

    public function rateAll(): RatingResult
    {
        $em = $this->em;

        return $em->getConnection()->transactional(function () use ($em): RatingResult {
            $pending = $em->createQuery(
                'SELECT e FROM App\\Domain\\Usage\\UsageEvent e WHERE e.status = :status ORDER BY e.id ASC'
            )
                ->setParameter('status', UsageStatus::Pending)
                ->getResult();

            $rated = 0;
            /** @var UnratedEvent[] $unrated */
            $unrated = [];
            $now = new DateTimeImmutable();

            foreach ($pending as $event) {
                $row = $this->applicablePriceRow($em, $event);
                if ($row === null) {
                    $unrated[] = new UnratedEvent(
                        $event->getProvider()->getCode(),
                        $event->getProviderRequestId(),
                        $event->getTs(),
                    );
                    continue;
                }

                $cost = $this->calculator->eventCost(
                    $event->getTokenCounts(),
                    $row,
                    $event->getProvider()->offPeakPolicy(),
                    TimeOfDay::minutesOfDayUtc($event->getTs()),
                );
                $event->markRated($cost, $row, $now);
                $rated++;
            }

            $em->flush();

            return new RatingResult($rated, $unrated);
        });
    }

    /**
     * Строка цен с наибольшим `valid_from <= ts` (п. 1) — или null,
     * если для модели события ещё нет ни одной применимой строки.
     */
    private function applicablePriceRow(EntityManagerInterface $em, UsageEvent $event): ?\App\Domain\Pricing\PriceRow
    {
        return $em->createQuery(
            'SELECT r FROM App\Domain\Pricing\PriceRow r
             WHERE r.model = :model
               AND r.validFrom <= :ts
             ORDER BY r.validFrom DESC'
        )
            ->setParameter('model', $event->getModel())
            ->setParameter('ts', $event->getTs(), \Doctrine\DBAL\Types\Types::DATETIME_IMMUTABLE)
            ->setMaxResults(1)
            ->getOneOrNullResult();
    }
}
