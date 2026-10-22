<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UsageEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class UsageEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsageEvent::class);
    }

    public function findByProviderRequest(string $provider, string $providerRequestId): ?UsageEvent
    {
        return $this->findOneBy(['provider' => $provider, 'providerRequestId' => $providerRequestId]);
    }

    /** @return UsageEvent[] */
    public function findUnrated(): array
    {
        return $this->findBy(['status' => UsageEvent::STATUS_UNRATED], ['id' => 'ASC']);
    }

    /** @return UsageEvent[] */
    public function findRatedByPriceIds(array $priceIds): array
    {
        if ($priceIds === []) {
            return [];
        }

        return $this->match(
            'e.appliedPriceId IN (:ids) AND e.status = :status',
            ['ids' => $priceIds, 'status' => \App\Entity\UsageEvent::STATUS_RATED],
        );
    }

    /**
     * @param string $dqlWhere дополнительный фрагмент DQL без WHERE
     * @param array  $params
     *
     * @return UsageEvent[]
     */
    private function match(string $dqlWhere, array $params): array
    {
        $q = $this->createQueryBuilder('e')->where($dqlWhere);
        foreach ($params as $k => $v) {
            $q->setParameter($k, $v);
        }

        return $q->getQuery()->getResult();
    }
}
