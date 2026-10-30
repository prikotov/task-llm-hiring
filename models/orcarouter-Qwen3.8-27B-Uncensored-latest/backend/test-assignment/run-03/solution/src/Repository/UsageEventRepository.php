<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UsageEvent;
use DateTimeImmutable;
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

    public function findByKey(string $provider, string $providerRequestId): ?UsageEvent
    {
        return $this->findOneBy([
            'provider' => $provider,
            'providerRequestId' => $providerRequestId,
        ]);
    }

    /**
     * События, ещё не рассчитанные (для app:billing:rate).
     *
     * @return UsageEvent[]
     */
    public function findUnrated(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.status = :st')
            ->orderBy('e.id', 'ASC')
            ->setParameter('st', UsageEvent::STATUS_UNRATED)
            ->getQuery()
            ->getResult();
    }

    /**
     * Рассчитанные события в отчётном периоде [from, to).
     *
     * @return UsageEvent[]
     */
    public function findRatedInRange(?DateTimeImmutable $from, ?DateTimeImmutable $to): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.status = :st')
            ->orderBy('e.user', 'ASC')
            ->addOrderBy('e.model', 'ASC')
            ->setParameter('st', UsageEvent::STATUS_RATED);

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')
                ->setParameter('from', $from);
        }

        if ($to !== null) {
            $qb->andWhere('e.ts < :to')
                ->setParameter('to', $to);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Рассчитанные события, тарифицированные конкретной строкой цен
     * (для ретро-коррекции — reprice).
     *
     * @return UsageEvent[]
     */
    public function findRatedByPriceRow(string $providerCode, string $modelCode, DateTimeImmutable $validFrom): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.status = :st')
            ->andWhere('e.provider = :pc')
            ->andWhere('e.model = :mc')
            ->andWhere('e.appliedValidFrom = :vf')
            ->setParameter('st', UsageEvent::STATUS_RATED)
            ->setParameter('pc', $providerCode)
            ->setParameter('mc', $modelCode)
            ->setParameter('vf', $validFrom)
            ->getQuery()
            ->getResult();
    }
}
