<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Iso8601;
use App\Domain\Money;
use App\Domain\RateCalculator;
use App\Entity\UsageEvent;
use App\Repository\ModelRepository;
use App\Repository\PriceRepository;
use App\Repository\ProviderRepository;
use App\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация событий (пункты 1–4 правил тарификации TASK.md):
 *  - выбор строки цен: наибольшее valid_from <= ts (valid_from включительно);
 *  - нет строки — событие остаётся unrated (повторно пробуют на каждом запуске);
 *  - оф-пиковая скидка по окну провайдера;
 *  - стоимость события — ровно одно округление до 6 знаков (HALF_UP).
 */
final class RatingService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly UsageEventRepository $events,
        private readonly ModelRepository $models,
        private readonly PriceRepository $prices,
        private readonly ProviderRepository $providers,
    ) {
    }

    /**
     * Тарифицирует все нерассчитанные события (включая повторные попытки unrated).
     *
     * @return array{rated: int, unrated: list<UsageEvent>}
     */
    public function rateAll(): array
    {
        return $this->em->wrapInTransaction(function (): array {
            $rated = 0;
            /** @var list<UsageEvent> $unrated */
            $unrated = [];

            foreach ($this->events->findUnrated() as $event) {
                if ($this->rateEvent($event) === null) {
                    $unrated[] = $event;
                    continue;
                }

                $rated++;
            }

            $this->em->flush();

            return ['rated' => $rated, 'unrated' => $unrated];
        });
    }

    /**
     * Вычисляет стоимость события и фиксирует её.
     *
     * @return Money|null null — строка цен не найдена, событие остаётся unrated
     */
    private function rateEvent(UsageEvent $event): ?Money
    {
        $model = $this->models->findByProviderCodeAndCode($event->provider, $event->model);
        if ($model === null) {
            return null;
        }

        $price = $this->prices->latestValidAt($model, $event->ts);
        if ($price === null) {
            return null;
        }

        $provider = $this->providers->findByCode($event->provider);
        $offPeak = $provider?->offPeak();

        $cost = RateCalculator::eventCost(
            $event->tokenUsage(),
            $price->pricingUnit(),
            $price->ratesByKind(),
            $offPeak,
            $event->ts,
        );

        $event->markRated($cost, $price);

        return $cost;
    }

    /** Строка stderr для unrated-события: «UNRATED <provider>/<id> <ts> no_price». */
    public static function unratedLine(UsageEvent $event): string
    {
        return sprintf(
            'UNRATED %s/%s %s no_price',
            $event->provider,
            $event->providerRequestId,
            Iso8601::formatUtc($event->ts),
        );
    }
}
