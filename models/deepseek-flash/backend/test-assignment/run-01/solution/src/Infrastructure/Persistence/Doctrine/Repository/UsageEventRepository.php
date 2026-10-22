<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Infrastructure\Persistence\Doctrine\Entity\ProviderModel;
use App\Infrastructure\Persistence\Doctrine\Entity\UsageEvent;
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

    /**
     * @return iterable<UsageEvent>
     */
    public function findUnrated(): iterable
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.costUsd IS NULL')
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->toIterable();
    }

    /**
     * @return iterable<UsageEvent>
     */
    public function findRatedInInterval(
        ProviderModel $model,
        DateTimeImmutable $from,
        ?DateTimeImmutable $to,
    ): iterable {
        $queryBuilder = $this->createQueryBuilder('e')
            ->andWhere('e.providerCode = :provider')
            ->andWhere('e.modelCode = :model')
            ->andWhere('e.costUsd IS NOT NULL')
            ->andWhere('e.occurredAt >= :from')
            ->setParameter('provider', $model->provider()->code())
            ->setParameter('model', $model->code())
            ->setParameter('from', $from)
            ->orderBy('e.id', 'ASC');

        if ($to !== null) {
            $queryBuilder
                ->andWhere('e.occurredAt < :to')
                ->setParameter('to', $to);
        }

        return $queryBuilder->getQuery()->toIterable();
    }
}
