<?php

declare(strict_types=1);

namespace App\Billing\Infrastructure\Persistence;

use App\Billing\Domain\Catalog\Price;
use App\Billing\Domain\Usage\UsageEvent;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<UsageEvent> */
final class UsageEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsageEvent::class);
    }

    public function existsByKey(string $provider, string $providerRequestId): bool
    {
        $count = $this->createQueryBuilder('e')
            ->select('COUNT(e.id)')
            ->andWhere('e.provider = :provider')
            ->andWhere('e.providerRequestId = :rid')
            ->setParameter('provider', $provider)
            ->setParameter('rid', $providerRequestId)
            ->getQuery()
            ->getSingleScalarResult();

        return (int) $count > 0;
    }

    /** @return list<UsageEvent> */
    public function findUnrated(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.price IS NULL')
            ->orderBy('e.ts', 'ASC')
            ->addOrderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Рассчитанные события модели с ts >= $from (для ретро-коррекции).
     *
     * @return list<UsageEvent>
     */
    public function findRatedForModelFrom(string $provider, string $model, DateTimeImmutable $from): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.provider = :provider')
            ->andWhere('e.model = :model')
            ->andWhere('e.ts >= :from')
            ->andWhere('e.price IS NOT NULL')
            ->setParameter('provider', $provider)
            ->setParameter('model', $model)
            ->setParameter('from', $from)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Рассчитанные события в периоде [from, to): from включительно, to исключительно.
     *
     * @return list<UsageEvent>
     */
    public function findRated(?DateTimeImmutable $from, ?DateTimeImmutable $to): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.price IS NOT NULL')
            ->orderBy('e.id', 'ASC');

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        return $qb->getQuery()->getResult();
    }
}
