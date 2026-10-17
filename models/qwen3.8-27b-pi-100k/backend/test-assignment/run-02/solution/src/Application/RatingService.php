<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Result\RateSummary;
use App\Domain\Billing\PriceSelection;
use App\Domain\Billing\PricingCalculator;
use App\Entity\UsageEvent;
use App\Repository\PriceRepository;
use App\Repository\ProviderRepository;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация событий (app:billing:rate).
 *
 * На каждом запуске повторяет попытку для ВСЕХ ещё не рассчитанных событий
 * (цены могли подтянуться позже — unrated не является ошибкой, п. 7).
 *
 * Правила:
 *  - выбор цены: строка модели с наибольшим valid_from <= ts (п. 1);
 *  - нет подходящей строки — событие остаётся unrated;
 *  - стоимость события (единственное округление) — до 6 знаков HALF_UP;
 *  - рассчитанное событие запоминает cost и id строки цен,
 *    по которой рассчитано (факт для reprice);
 *  - уже рассчитанные события повторно не пересчитываются.
 */
final class RatingService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
        private readonly ProviderRepository $providers,
        private readonly PriceRepository $prices,
        private readonly PricingCalculator $calculator,
    ) {
    }

    public function rate(): RateSummary
    {
        return $this->em->wrapInTransaction(function (): RateSummary {
            $unrated = $this->events->findUnrated();
            if ($unrated === []) {
                return new RateSummary(0, 0, []);
            }

            // Справочник небольшой (провайдеры/модели/цены) — читаем его
            // целиком и группируем в памяти, не ходя в БД по каждому событию.
            $offPeakByProvider = [];
            foreach ($this->providers->findAll() as $provider) {
                $offPeakByProvider[$provider->code()] = $provider->offPeakPolicy();
            }

            $rowsByPair = [];
            foreach ($this->prices->findAll() as $row) {
                $pair = $row->model()->provider()->code()."\0".$row->model()->code();
                $rowsByPair[$pair][] = $row;
            }

            $rated = 0;
            $unratedEvents = [];

            foreach ($unrated as $event) {
                $pair = $event->providerCode()."\0".$event->modelCode();
                $rows = $rowsByPair[$pair] ?? [];
                $best = PriceSelection::best($event->ts(), $rows);

                if ($best === null) {
                    $unratedEvents[] = $event;
                    continue;
                }

                $cost = $this->calculator->price(
                    $best,
                    $event->tokenUsage(),
                    $offPeakByProvider[$event->providerCode()] ?? null, // нет провайдера в справочнике — нет и скидки
                    $event->ts(),
                );

                $event->applyRating($cost, (int) $best->id());
                $rated++;
            }

            return new RateSummary($rated, \count($unratedEvents), $unratedEvents);
        });
    }
}
