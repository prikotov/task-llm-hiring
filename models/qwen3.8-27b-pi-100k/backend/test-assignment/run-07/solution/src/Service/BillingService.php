<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\BillingComputer;
use App\Domain\Money;
use App\Domain\PriceUnit;
use App\Domain\RateRun;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий (app:billing:rate).
 *
 * Для каждого pending-события:
 *  1) выбор цены — строка модели с максимальным valid_from <= ts;
 *  2) точный расчёт по схеме (per_1m_tokens | per_1m_tokens_unified);
 *  3) оф-пик провайдера (если UTC-момент в окне);
 *  4) единственное округление HALF_UP до 6 знаков.
 *
 * Событие без подходящей цены остаётся pending (unrated) — каждый
 * следующий запуск rate повторяет попытку (цены могли «догрузиться»).
 */
final class BillingService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BillingComputer $computer,
    ) {
    }

    public function rate(): RateRun
    {
        return $this->em->wrapInTransaction(function (): RateRun {
            $snapshot = CatalogSnapshot::load($this->em);

            /** @var UsageEvent[] $pending */
            $pending = $this->em->getRepository(UsageEvent::class)->findBy(
                ['status' => UsageEvent::STATUS_PENDING],
                ['id' => 'ASC'],
            );

            $rated = 0;
            $unrated = [];

            foreach ($pending as $event) {
                $priceRow = $snapshot->findPriceRow($event->getProvider(), $event->getModel(), $event->getTs());
                if ($priceRow === null) {
                    $unrated[] = $event;
                    continue;
                }

                $cost = $this->computer
                    ->calculate(
                        $event->getTokensInput(),
                        $event->getTokensOutput(),
                        $event->getTokensCacheRead(),
                        $event->getTokensCacheWrite(),
                        PriceUnit::from($priceRow->getUnit()),
                        $priceRow->getRates(),
                        $snapshot->offPeakFor($event->getProvider()),
                        $event->getTs(),
                    )
                    ->roundHalfUp();

                $event->markRated($cost, $priceRow);
                $rated++;
            }

            $this->em->flush();

            $leftUnrated = (int) $this->em->createQuery(
                'SELECT COUNT(e.id) FROM ' . UsageEvent::class . ' e WHERE e.status = :status'
            )
                ->setParameter('status', UsageEvent::STATUS_PENDING)
                ->getSingleScalarResult();

            return new RateRun($rated, $leftUnrated, $unrated);
        });
    }
}
