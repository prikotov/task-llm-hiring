<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Usage\BillingStatus;
use App\Domain\Usage\UsageEvent;
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

    public function findByNaturalKey(string $provider, string $providerRequestId): ?UsageEvent
    {
        return $this->findOneBy([
            'provider' => $provider,
            'providerRequestId' => $providerRequestId,
        ]);
    }

    /**
     * Все ещё не рассчитанные события — кандидаты на повторный расчёт.
     *
     * @return UsageEvent[]
     */
    public function findUnrated(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.status = :status')
            ->orderBy('e.id', 'ASC')
            ->setParameter('status', BillingStatus::Unrated)
            ->getQuery()
            ->getResult();
    }

    /**
     * Рассчитанные события указанных моделей — для пересчёта при reprice.
     *
     * @param list<int> $modelIds
     *
     * @return UsageEvent[]
     */
    public function findRatedForModelIds(array $modelIds): array
    {
        if ($modelIds === []) {
            return [];
        }

        return $this->createQueryBuilder('e')
            ->andWhere('e.status = :status')
            ->andWhere('e.model IN (:modelIds)')
            ->orderBy('e.id', 'ASC')
            ->setParameter('status', BillingStatus::Rated)
            ->setParameter('modelIds', $modelIds)
            ->getQuery()
            ->getResult();
    }

    /**
     * Рассчитанные события в отчётном периоде: ts ∈ [$from, $to)
     * (граничные null — без ограничения; правила п. 6).
     *
     * @return UsageEvent[]
     */
    public function findRatedInPeriod(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $qb = $this->createQueryBuilder('e')
            ->andWhere('e.status = :status')
            ->setParameter('status', BillingStatus::Rated);

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        return $qb->orderBy('e.id', 'ASC')->getQuery()->getResult();
    }
}
