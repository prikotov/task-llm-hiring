<?php

declare(strict_types=1);

namespace App\Application\Persistence;

use App\Domain\Billing\Cost;
use App\Domain\Billing\PriceCatalog;
use App\Domain\Billing\ProviderInfo;
use App\Domain\Billing\RatedEventStore;
use App\Domain\Pricing\Provider;
use App\Domain\Pricing\StoredPriceRow;
use App\Domain\Usage\RatedEventEntity;
use App\Domain\Usage\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Реализации портов (PriceCatalog, RatedEventStore) на базе Doctrine.
 *
 * Идемпотентная запись RatedEventStore::saveRated() — повторная запись
 * по естественному ключу (provider, provider_request_id) не обновляет
 * и не доначисляет (TASK.md п. 5).
 */
final class InMemoryPricingCatalog implements PriceCatalog, RatedEventStore
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    // --- PriceCatalog ---

    public function findProvider(string $code): ?ProviderInfo
    {
        $provider = $this->em
            ->getRepository(Provider::class)
            ->findOneBy(['code' => $code]);

        if ($provider === null) {
            return null;
        }

        return new ProviderInfo($provider->code, $provider->offPeak());
    }

    public function findPriceRowFor(
        string $provider,
        string $model,
        \DateTimeImmutable $ts,
    ): ?StoredPriceRow {
        return $this->em
            ->createQueryBuilder()
            ->select('p')
            ->from(StoredPriceRow::class, 'p')
            ->where('p.provider = :prov')
            ->andWhere('p.model = :model')
            ->andWhere('p.validFrom <= :ts')
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->setParameter('prov', $provider)
            ->setParameter('model', $model)
            ->setParameter('ts', $ts->format('Y-m-d\TH:i:s\\Z'), \Doctrine\DBAL\Types\Types::STRING)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return StoredPriceRow[] */
    public function allPriceRowsFor(string $provider, string $model): array
    {
        return $this->em
            ->getRepository(StoredPriceRow::class)
            ->findBy(
                ['provider' => $provider, 'model' => $model],
                ['validFrom' => 'ASC'],
            );
    }

    /** @return string[] */
    public function providerModels(string $provider): array
    {
        $rows = $this->em
            ->createQueryBuilder()
            ->select('m.code')
            ->from(\App\Domain\Pricing\Model::class, 'm')
            ->where('m.provider = :p')
            ->setParameter('p', $provider)
            ->getQuery()
            ->getSingleColumnResult();

        return array_values(array_map('strval', $rows));
    }

    // --- RatedEventStore ---

    public function saveRated(UsageEvent $event, Cost $cost): void
    {
        $em = $this->em;
        $existing = $em->getRepository(RatedEventEntity::class)
            ->findOneBy(['provider' => $event->provider, 'providerRequestId' => $event->providerRequestId]);
        if ($existing !== null) {
            return; // идемпотентность (TASK.md п. 5)
        }

        $entity = new RatedEventEntity();
        $entity->provider = $event->provider;
        $entity->providerRequestId = $event->providerRequestId;
        $entity->model = $event->model;
        $entity->user = $event->user;
        $entity->ts = $event->ts;
        $entity->tokensInput = $event->tokens->input;
        $entity->tokensOutput = $event->tokens->output;
        $entity->tokensCacheRead = $event->tokens->cacheRead;
        $entity->tokensCacheWrite = $event->tokens->cacheWrite;
        $entity->costUsd = $cost->toSixDecimalsTruncated();
        $entity->originalCostUsd = $entity->costUsd;
        $entity->repricedCount = 0;
        $em->persist($entity);
    }
}
