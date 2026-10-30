<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\Pricing\Money;
use App\Domain\Pricing\RateCalculator;
use App\Entity\Price;
use App\Entity\Provider;
use App\Entity\UsageEvent;
use App\Repository\ModelRepository;
use App\Repository\PriceRepository;
use App\Repository\ProviderRepository;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий.
 *
 * Алгоритм (rule 1, 3, 4):
 *  - для каждого unrated-события ищем строку цен модели — largest valid_from <= ts;
 *  - если строка есть — считаем точную стоимость (Money) и применяем оф-пик;
 *  - единственное округление — HALF_UP до 6 знаков при записи итоговой стоимости;
 *  - если строки нет — событие остаётся unrated (rule 7).
 */
final class RateService
{
    public function __construct(
        private EntityManagerInterface $em,
        private UsageEventRepository $events,
        private ProviderRepository $providers,
        private PriceRepository $prices,
    ) {
    }

    /**
     * Возвращает [rated_count, unrated_count] для этого запуска.
     */
    public function rateAll(): array
    {
        $rated = 0;
        $unrated = 0;
        $unratedItems = [];

        $items = $this->events->findUnrated();
        $utc = new \DateTimeZone('UTC');

        foreach ($items as $event) {
            $priceRow = $this->prices->findPricedAt(
                $event->provider(),
                $event->modelCode(),
                $event->ts(),
            );

            if ($priceRow === null) {
                $unrated++;
                $unratedItems[] = [
                    'provider' => $event->provider(),
                    'requestId' => $event->providerRequestId(),
                    'ts' => $event->ts()->setTimezone($utc)->format('c'),
                ];
                continue;
            }

            $provider = $this->providers->findByCode($event->provider());
            $offPeak = $provider?->offPeak();
            $calculator = new RateCalculator($offPeak, $priceRow->pricedModel());

            $hhmmUtc = $event->ts()->setTimezone($utc)->format('H:i');
            $cost = $calculator->price($event->tokens(), $hhmmUtc);

            $event->markRated($cost->format(), $priceRow->id());
            $rated++;
        }

        $this->em->flush();
        $this->em->clear();

        return [$rated, $unrated, $unratedItems];
    }
}
