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
    public function findUnrated(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.isRated = :rated')
            ->andWhere('e.ts >= :from')
            ->andWhere('e.ts < :to')
            ->setParameter('rated', false)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->getQuery()
            ->getResult();
    }

    /**
     * Finds usage events for report within a date range.
     * 
     * @return UsageEvent[]
     */
    public function findForReport(\DateTimeImmutable $from, \DateTimeImmutable $to): array
    {
        return $this->createQueryBuilder('e')
            ->join('e.model', 'm')
            ->join('m.provider', 'p')
            ->where('e.isRated = :rated')
            ->andWhere('e.ts >= :from')
            ->andWhere('e.ts < :to')
            ->setParameter('rated', true)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->orderBy('e.user', 'ASC')
            ->addOrderBy('p.code', 'ASC')
            ->addOrderBy('m.code', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
