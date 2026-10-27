<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UsageEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<UsageEvent> */
final class UsageEventRepository extends ServiceEntityRepository implements ServiceEntityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsageEvent::class);
    }
    /**
     * @return UsageEvent[]
     */
    public function findRated(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $qb = $this->createQueryBuilder('e')->where('e.status = :status')->setParameter('status', UsageEvent::STATUS_RATED);

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        return $qb->getQuery()->getResult();
    }
}
