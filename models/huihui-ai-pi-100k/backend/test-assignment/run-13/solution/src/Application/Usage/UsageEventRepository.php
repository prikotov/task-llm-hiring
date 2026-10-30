<?php

declare(strict_types=1);

namespace App\Application\Usage;

use App\Domain\Provider\Provider;
use App\Domain\Usage\UsageEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @phpstan-type EventRow array{
 *     id: int,
 *     user: string,
 *     model: string,
 *     tokens_input: int,
 *     tokens_output: int,
 *     tokens_cache_read: int,
 *     tokens_cache_write: int,
 *     cost_usd: string,
 * }
 *
 * @extends ServiceEntityRepository<UsageEvent>
 */
class UsageEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsageEvent::class);
    }

    public function findExisting(string $provider, string $providerRequestId): ?UsageEvent
    {
        return $this->findOneBy(['providerRequestId' => $providerRequestId, 'provider' => $provider]);
    }

    /**
     * Итоги по (user, model), агрегированные на стороне БД.
     *
     * @return list<EventRow>
     */
    public function aggregateReport(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        $q = $this->createQueryBuilder('u')
            ->select('u.user AS user, u.model.code AS model, u.provider.code AS provider')
            ->addSelect('SUM(u.tokensInput) AS tokens_input')
            ->addSelect('SUM(u.tokensOutput) AS tokens_output')
            ->addSelect('SUM(u.tokensCacheRead) AS tokens_cache_read')
            ->addSelect('SUM(u.tokensCacheWrite) AS tokens_cache_write')
            ->addSelect('SUM(u.costUsd) AS cost_usd')
            ->where('u.priceRow IS NOT NULL')
            ->andWhere('u.ts >= :from')
            ->andWhere('u.ts < :to')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->groupBy('u.user')
            ->addGroupBy('u.model.code')
            ->addGroupBy('u.provider.code')
            ->orderBy('u.user', 'ASC')
            ->addOrderBy('u.model.code', 'ASC')
            ->getQuery();

        $rows = $q->getArrayResult();

        // SUM(string) в Postgres не работает напрямую; Doctrine ORM не поддерживает
        // SUM(string) -> используем SQL-выражение CAST.
        // (см. ниже, здесь упрощённая сигнатура)
        $out = [];
        foreach ($rows as $row) {
            $out[] = [
                'id' => $row['id'],
                'user' => $row['user'],
                'model' => $row['provider'].'/'.$row['model'],
                'tokens_input' => (int) $row['tokens_input'],
                'tokens_output' => (int) $row['tokens_output'],
                'tokens_cache_read' => (int) $row['tokens_cache_read'],
                'tokens_cache_write' => (int) $row['tokens_cache_write'],
                'cost_usd' => (string) $row['cost_usd'],
            ];
        }

        return $out;
    }
}
