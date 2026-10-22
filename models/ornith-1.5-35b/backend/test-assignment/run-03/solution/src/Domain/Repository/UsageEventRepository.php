<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\UsageEvent;
use App\Domain\Repository\UsageEventRepository;
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

    public function save(UsageEvent $event): void
    {
        $this->getEntityManager()->persist($event);
        $this->getEntityManager()->flush();
    }

    public function findByKey(string $providerId, string $providerRequestId): ?UsageEvent
    {
        /** @var UsageEvent|null $event */
        return $this->findOneBy([
            'providerId' => $providerId,
            'providerRequestId' => $providerRequestId,
        ]);
    }

    /**
     * События, тарифицированные по одной из указанных строк цены.
     *
     * @param int[] $priceRowIds
     * @return UsageEvent[]
     */
    public function findByPriceRowIds(array $priceRowIds): array
    {
        if ([] === $priceRowIds) {
            return [];
        }

        /** @var UsageEvent[] $events */
        return $this->createQueryBuilder('e')
            ->where('e.priceRowId IN (:ids)')
            ->setParameter('ids', $priceRowIds)
            ->getQuery()
            ->getResult();
    }

    /**
     * События без тарификации, с учётом окна периода [from, to).
     *
     * @return UsageEvent[]
     */
    public function findUnrated(?\DateTimeImmutable $from = null, ?\DateTimeImmutable $to = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.rated = :rated')
            ->setParameter('rated', false)
            ->orderBy('e.providerId', 'ASC')
            ->addOrderBy('e.providerRequestId', 'ASC');

        if (null !== $from) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }

        if (null !== $to) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        /** @var UsageEvent[] $events */
        return $qb->getQuery()->getResult();
    }

    /**
     * Все тарифицированные события в окне периода (для отчёта).
     *
     * @return UsageEvent[]
     */
    public function findRated(?\DateTimeImmutable $from = null, ?\DateTimeImmutable $to = null): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.rated = :rated')
            ->setParameter('rated', true)
            ->orderBy('e.user', 'ASC')
            ->addOrderBy('e.model', 'ASC');

        if (null !== $from) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }

        if (null !== $to) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        /** @var UsageEvent[] $events */
        return $qb->getQuery()->getResult();
    }
}
