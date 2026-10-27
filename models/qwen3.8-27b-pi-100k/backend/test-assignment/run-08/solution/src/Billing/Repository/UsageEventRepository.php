<?php

declare(strict_types=1);

namespace App\Billing\Repository;

use App\Billing\Entity\UsageEvent;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class UsageEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsageEvent::class);
    }

    /**
     * Все ещё не рассчитанные события (дeterministic порядок).
     *
     * @return list<UsageEvent>
     */
    public function findAllPending(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->setParameter('status', \App\Billing\Enum\UsageEventStatus::Pending->value)
            ->orderBy('e.provider', 'ASC')
            ->addOrderBy('e.providerRequestId', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Рассчитанные события с ts в диапазоне [from, to) (границы — null означает без ограничения).
     *
     * @return list<UsageEvent>
     */
    public function findRatedInRange(?DateTimeImmutable $from, ?DateTimeImmutable $to): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->setParameter('status', \App\Billing\Enum\UsageEventStatus::Rated->value);

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        return $qb
            ->orderBy('e.user', 'ASC')
            ->addOrderBy('e.provider', 'ASC')
            ->addOrderBy('e.model', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Рассчитанные события, тарифицированные строкой цен с данным id.
     *
     * @return list<UsageEvent>
     */
    public function findRatedByPriceRow(int $priceRowId): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.priceRowId = :id')
            ->setParameter('id', $priceRowId)
            ->orderBy('e.provider', 'ASC')
            ->addOrderBy('e.providerRequestId', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
