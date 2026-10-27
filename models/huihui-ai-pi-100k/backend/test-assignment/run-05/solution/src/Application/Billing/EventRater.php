<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Billing\EventPricer;
use App\Domain\Billing\OffPeak;
use App\Domain\Billing\TokenUsage;
use App\Infrastructure\Persistence\Entity\PriceRow;
use App\Infrastructure\Persistence\Entity\Provider;
use App\Infrastructure\Persistence\Entity\UsageEvent;
use App\Infrastructure\Persistence\PriceRowMapper;
use App\Infrastructure\Persistence\Repository\PriceRowRepository;
use App\Infrastructure\Persistence\Repository\UsageEventRepository;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация событий (команда app:billing:rate, TASK.md правила 1–4).
 *
 * Запуск идемпотентен и «добирает»: каждый раз обрабатываются все
 * ещё unrated-события — если цены подтянулись позже, unrated снова
 * попадают в попытку (TASK.md, п. 7).
 */
final class EventRater
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly EventPricer $pricer,
    ) {
    }

    public function rate(): RatingResult
    {
        $em = $this->em;
        $eventsRepo = $em->getRepository(UsageEvent::class);
        $priceRepo = $em->getRepository(PriceRow::class);
        $providerRepo = $em->getRepository(Provider::class);

        /** @var UsageEvent[] $events */
        $events = $eventsRepo->findByStatus(UsageEvent::STATUS_UNRATED);

        $rated = 0;
        /** @var UsageEvent[] $unratedEvents */
        $unratedEvents = [];
        /** @var array<string, OffPeak|null> $offPeakCache */
        $offPeakCache = [];

        foreach ($events as $event) {
            $priceRow = $priceRepo->latestAtOrBefore($event->provider, $event->model, $event->ts);

            if ($priceRow === null) {
                $unratedEvents[] = $event;
                continue;
            }

            if (!array_key_exists($event->provider, $offPeakCache)) {
                $offPeakCache[$event->provider] = self::resolveOffPeak($providerRepo, $event->provider);
            }

            $cost = $this->pricer->price(
                PriceRowMapper::toSpec($priceRow),
                TokenUsage::create(
                    $event->tokensInput,
                    $event->tokensOutput,
                    $event->tokensCacheRead,
                    $event->tokensCacheWrite,
                ),
                $offPeakCache[$event->provider],
                $event->ts,
            );

            $event->status = UsageEvent::STATUS_RATED;
            $event->cost = $cost;
            $event->priceValidFrom = $priceRow->validFrom;
            $rated++;
        }

        $em->flush();
        return new RatingResult(rated: $rated, unratedEvents: $unratedEvents);
    }

    /**
     * @param \Doctrine\ORM\EntityRepository<Provider> $providerRepo
     */
    private static function resolveOffPeak(\Doctrine\ORM\EntityRepository $providerRepo, string $code): ?OffPeak
    {
        $provider = $providerRepo->findOneBy(['code' => $code]);

        if ($provider === null) {
            return null;
        }

        return OffPeak::fromWindow(
            $provider->offPeakStart,
            $provider->offPeakEnd,
            $provider->offPeakMultiplier,
        );
    }
}
