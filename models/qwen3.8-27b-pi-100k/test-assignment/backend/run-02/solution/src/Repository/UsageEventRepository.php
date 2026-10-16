<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UsageEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UsageEvent>
 */
class UsageEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsageEvent::class);
    }

    public function findByProviderAndRequestId(string $providerCode, string $providerRequestId): ?UsageEvent
    {
        return $this->createQueryBuilder('e')
            ->where('e.providerCode = :providerCode')
            ->andWhere('e.providerRequestId = :providerRequestId')
            ->setParameter('providerCode', $providerCode)
            ->setParameter('providerRequestId', $providerRequestId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Все ещё не рассчитанные события (cost IS NULL).
     *
     * @return UsageEvent[]
     */
    public function findUnrated(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.cost IS NULL')
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Рассчитанные события, тарифицированные строками цен с этими id —
     * для reprice: именно они должны быть пересчитаны.
     *
     * @param int[] $priceIds
     *
     * @return UsageEvent[]
     */
    public function findRatedByPriceIdIn(array $priceIds): array
    {
        if ($priceIds === []) {
            return [];
        }

        return $this->createQueryBuilder('e')
            ->where('e.cost IS NOT NULL')
            ->andWhere('e.priceId IN (:priceIds)')
            ->setParameter('priceIds', array_values(array_unique($priceIds)))
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
