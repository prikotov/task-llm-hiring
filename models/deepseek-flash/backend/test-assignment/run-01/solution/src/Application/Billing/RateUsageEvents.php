<?php

declare(strict_types=1);

namespace App\Application\Billing;

use App\Domain\Pricing\CostCalculator;
use App\Infrastructure\Persistence\Doctrine\Entity\Provider;
use App\Infrastructure\Persistence\Doctrine\Entity\ProviderModel;
use App\Infrastructure\Persistence\Doctrine\Repository\PriceRepository;
use App\Infrastructure\Persistence\Doctrine\Repository\ProviderModelRepository;
use App\Infrastructure\Persistence\Doctrine\Repository\ProviderRepository;
use App\Infrastructure\Persistence\Doctrine\Repository\UsageEventRepository;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Prices every event that has no cost yet. Unrated events intentionally remain
 * unrated and are retried on the next invocation.
 */
final readonly class RateUsageEvents
{
    private const int FLUSH_BATCH = 200;

    public function __construct(
        private EntityManagerInterface $entityManager,
        private UsageEventRepository $events,
        private ProviderRepository $providers,
        private ProviderModelRepository $models,
        private PriceRepository $prices,
        private CostCalculator $calculator,
    ) {
    }

    public function rate(): RatingResult
    {
        $rated = 0;
        $unrated = [];
        $now = new DateTimeImmutable('now', new DateTimeZone('UTC'));

        /** @var array<string, Provider|null> $providerCache */
        $providerCache = [];
        /** @var array<string, ProviderModel|null> $modelCache */
        $modelCache = [];

        foreach ($this->events->findUnrated() as $event) {
            $providerCode = $event->providerCode();
            if (array_key_exists($providerCode, $providerCache)) {
                $provider = $providerCache[$providerCode];
            } else {
                $provider = $this->providers->findByCode($providerCode);
                $providerCache[$providerCode] = $provider;
            }

            $modelKey = $providerCode . "\0" . $event->modelCode();
            if (array_key_exists($modelKey, $modelCache)) {
                $model = $modelCache[$modelKey];
            } else {
                $model = $provider === null
                    ? null
                    : $this->models->findByProviderAndCode($provider, $event->modelCode());
                $modelCache[$modelKey] = $model;
            }

            $price = $model === null
                ? null
                : $this->prices->findApplicableForModel($model, $event->occurredAt());

            if ($provider === null || $price === null) {
                $unrated[] = new UnratedEvent(
                    $event->providerCode(),
                    $event->providerRequestId(),
                    $event->occurredAt(),
                );

                continue;
            }

            $cost = $this->calculator->calculate(
                $event->usage(),
                $price->rateCard(),
                $provider->offPeakWindow(),
                $event->occurredAt(),
            );
            $event->markRated($price, $cost, $now);
            ++$rated;

            if ($rated % self::FLUSH_BATCH === 0) {
                $this->entityManager->flush();
            }
        }

        $this->entityManager->flush();

        return new RatingResult($rated, count($unrated), $unrated);
    }
}
