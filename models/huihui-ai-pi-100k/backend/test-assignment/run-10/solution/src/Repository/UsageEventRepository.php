<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Billing\EventStatus;
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

    public function existsByKey(string $providerCode, string $providerRequestId): bool
    {
        return $this->count(['providerCode' => $providerCode, 'providerRequestId' => $providerRequestId]) > 0;
    }

    /**
     * События, ожидающие расчёта (pending).
     */
    public function findPending(): iterable
    {
        return $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->setParameter('status', EventStatus::Pending)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * События, рассчитанные в периоде [from, to).
     *
     * @return iterable<UsageEvent>
     */
    public function findRatedBetween(\DateTimeImmutable|null $from, \DateTimeImmutable|null $to): iterable
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->setParameter('status', EventStatus::Rated)
            ->orderBy('e.user', 'ASC')
            ->addOrderBy('e.providerCode', 'ASC')
            ->addOrderBy('e.modelCode', 'ASC');

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Рассчитанные события, тарировавшиеся данной строкой цены
     * (для app:billing:reprice).
     *
     * @return iterable<UsageEvent>
     */
    public function findRatedByPriceRow(int $priceRowId): iterable
    {
        return $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->andWhere('e.priceRow IS NOT NULL')
            ->andWhere('e.priceRow = :priceRow')
            ->setParameter('status', EventStatus::Rated)
            ->setParameter('priceRow', $priceRowId)
            ->getQuery()
            ->getResult();
    }
}
