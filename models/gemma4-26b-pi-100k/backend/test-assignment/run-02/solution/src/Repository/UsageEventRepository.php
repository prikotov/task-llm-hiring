<?php

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

    /**
     * @return UsageEvent[]
     */
    public function findUnrated(\DateTimeImmutable $since): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.isRated = :isRated')
            ->andWhere('e.ts >= :since')
            ->setParameter('isRated', false)
            ->setParameter('since', $since)
            ->getQuery()
            ->getResult();
    }

    /**
     * @return UsageEvent[]
     */
    public function findForReport(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.isRated = :isRated')
            ->andWhere('e.ts >= :from')
            ->andWhere('e.ts < :to')
            ->setParameter('isRated', true)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('e.user', 'ASC')
            ->addOrderBy('e.model', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
