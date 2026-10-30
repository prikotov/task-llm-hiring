<?php

declare(strict_types=1);

namespace App\Repository\Billing;

use App\Entity\Billing\UsageEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UsageEvent>
 */
final class UsageEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsageEvent::class);
    }

    public function countByNaturalKey(string $provider, string $providerRequestId): int
    {
        return (int) $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->where('e.provider = :provider')
            ->andWhere('e.providerRequestId = :requestId')
            ->setParameter('provider', $provider)
            ->setParameter('requestId', $providerRequestId)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
