<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UsageEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Журнал событий использования.
 */
final class UsageEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsageEvent::class);
    }

    public function byNaturalKey(string $provider, string $requestId): ?UsageEvent
    {
        return $this->findOneBy(['provider' => $provider, 'providerRequestId' => $requestId]);
    }

    /** События, ожидающие расчёта: status 'pending' или 'unrated'. */
    public function pendingEvents(): array
    {
        return $this->createQueryBuilder('s')
            ->where('s.status IN (:st, :st2)')
            ->orderBy('s.id', 'ASC')
            ->setParameter('st', UsageEvent::STATUS_PENDING)
            ->setParameter('st2', UsageEvent::STATUS_UNRATED)
            ->getQuery()
            ->getResult();
    }

    /** События для отчёта: rated + в период [from, to). */
    public function forReport(?\DateTimeInterface $from, ?\DateTimeInterface $to): array
    {
        $q = $this->createQueryBuilder('s')
            ->where('s.status = :rated')
            ->setParameter('rated', UsageEvent::STATUS_RATED);

        if ($from !== null) {
            $q->andWhere('s.ts >= :from')
              ->setParameter('from', \DateTimeImmutable::createFromInterface($from)->setTimezone(new \DateTimeZone('UTC')));
        }
        if ($to !== null) {
            $q->andWhere('s.ts < :to')
              ->setParameter('to', \DateTimeImmutable::createFromInterface($to)->setTimezone(new \DateTimeZone('UTC')));
        }

        return $q->getQuery()->getResult();
    }

    /**
     * Все rated-события, закреплённые за строкой цены из $priceIds. Для
     * reprice-пересчёта.
     *
     * @param int[] $priceIds
     * @return UsageEvent[]
     */
    public function ratedByPriceIds(array $priceIds): array
    {
        if ($priceIds === []) {
            return [];
        }
        $ids = array_map('intval', $priceIds);

        return $this->createQueryBuilder('s')
            ->where('s.status = :rated')
            ->andWhere('s.pricedBy IN (:ids)')
            ->setParameter('rated', UsageEvent::STATUS_RATED)
            ->setParameter('ids', $ids)
            ->getQuery()
            ->getResult();
    }
}
