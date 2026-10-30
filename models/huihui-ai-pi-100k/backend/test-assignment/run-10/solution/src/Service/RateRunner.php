<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Billing\CostCalculator;
use App\Repository\PriceRowRepository;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Расчёт стоимости нерассчитанных событий (app:billing:rate).
 *
 * - выбирает цену по правилу 1 (строка с наибольшим valid_from <= ts);
 * - считает стоимость (правила 2, 3, 4 через CostCalculator);
 * - повторяет попытку для всех pending-событий (правило 7:
 *   цены могли подтянуться позже);
 * - не находит цену — событие остаётся pending (unrated) и в этом запуске
 *   отчётится в stderr.
 */
final class RateRunner
{
    public function __construct(
        private EntityManagerInterface $em,
        private UsageEventRepository $events,
        private PriceRowRepository $priceRows,
        private CostCalculator $calc,
    ) {
    }

    /**
     * Возврат: число новых (в этом запуске) rated и список unrated
     * событий (для stderr).
     *
     * @return array{rated:int, unrated: array<int, array{provider:string, request:string, ts:string}>}
     */
    public function rate(): array
    {
        $now = new \DateTimeImmutable();
        $unrated = [];
        $rated = 0;

        $this->em->wrapInTransaction(function () use ($now, &$unrated, &$rated): void {
            // Сначала собираем весь pending — материализованный массив,
            // а не ленивый курсор. Это позволяет делать запросы к ценам
            // на том же соединении без конфликта с открытым result set.
            $pending = $this->events->findPending();
            $list = \is_array($pending) ? $pending : \iterator_to_array($pending, false);

            foreach ($list as $event) {
                $priceRow = $this->priceRows->findForModelAt(
                    $event->providerCode(),
                    $event->modelCode(),
                    $event->ts(),
                );

                if ($priceRow === null) {
                    $unrated[] = [
                        'provider' => $event->providerCode(),
                        'request' => $event->providerRequestId(),
                        'ts' => $event->ts()->setTimezone(new \DateTimeZone('UTC'))->format('Y-m-d\TH:i:s\Z'),
                    ];
                    continue;
                }

                $cost = $this->calc->rate(
                    $event->counts(),
                    $priceRow->unit(),
                    $priceRow->rates(),
                    $priceRow->provider()->offPeak(),
                    $event->minuteOfDayUtc(),
                );

                $event->markRated($priceRow, $cost, $now);
                $rated++;
            }
        });

        return ['rated' => $rated, 'unrated' => $unrated];
    }
}
