<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Infrastructure\Persistence\Entity\UsageEvent;
use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<UsageEvent>
 */
final class UsageEventRepository extends EntityRepository
{
    /**
     * @return UsageEvent[]
     */
    public function findByStatus(string $status): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->setParameter('status', $status)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Рассчитанные события в периоде [from, to).
     *
     * @return UsageEvent[]
     */
    public function findRatedBetween(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->setParameter('status', UsageEvent::STATUS_RATED);

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        return $qb
            ->orderBy('e.ts', 'ASC')
            ->addOrderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Рассчитанные события, тарифицированные строкой цен
     * (provider, model, validFrom) — для ретро-коррекции.
     *
     * @return UsageEvent[]
     */
    public function findRatedByPriceRow(string $provider, string $model, \DateTimeImmutable $validFrom): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->setParameter('status', UsageEvent::STATUS_RATED)
            ->andWhere('e.provider = :provider')
            ->setParameter('provider', $provider)
            ->andWhere('e.model = :model')
            ->setParameter('model', $model)
            ->andWhere('e.priceValidFrom = :validFrom')
            ->setParameter('validFrom', $validFrom)
            ->getQuery()
            ->getResult();
    }
}
