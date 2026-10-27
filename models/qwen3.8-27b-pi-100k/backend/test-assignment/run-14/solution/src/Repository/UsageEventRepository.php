<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UsageEvent;
use App\Entity\UsageEvent as Ev;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UsageEvent>
 */
final class UsageEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ev::class);
    }

    /**
     * Событие по natural key, если уже существует.
     */
    public function findByProviderAndRequestId(string $provider, string $providerRequestId): ?Ev
    {
        return $this->findOneBy([
            'provider' => $provider,
            'providerRequestId' => $providerRequestId,
        ]);
    }

    /**
     * Все ещё не рассчитанные события (включая повторные попытки unrated).
     */
    public function findUnrated(): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->setParameter('status', Ev::STATUS_UNRATED)
            ->orderBy('e.ts', 'ASC')->orderBy('e.provider', 'ASC')->orderBy('e.providerRequestId', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * События с заданным priceRowId — используются для reprice.
     */
    public function findRatedByPriceRow(int $priceRowId): array
    {
        return $this->createQueryBuilder('e')
            ->where('e.priceRowId = :id')
            ->andWhere('e.status = :status')
            ->setParameter('id', $priceRowId)
            ->setParameter('status', Ev::STATUS_RATED)
            ->orderBy('e.ts', 'ASC')->orderBy('e.provider', 'ASC')->orderBy('e.providerRequestId', 'ASC')
            ->getQuery()
            ->getResult();
    }
}
