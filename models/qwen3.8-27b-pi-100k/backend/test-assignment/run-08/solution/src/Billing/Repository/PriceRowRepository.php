<?php

declare(strict_types=1);

namespace App\Billing\Repository;

use App\Billing\Entity\PriceRow;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class PriceRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceRow::class);
    }

    public function findByNaturalKey(string $provider, string $model, DateTimeImmutable $validFrom): ?PriceRow
    {
        return $this->findOneBy([
            'provider' => $provider,
            'model' => $model,
            'validFrom' => $validFrom,
        ]);
    }

    /**
     * Строка цен, действовавшая в момент ts: наибольшее valid_from ≤ ts для (provider, model).
     */
    public function findApplicable(string $provider, string $model, DateTimeImmutable $ts): ?PriceRow
    {
        return $this->createQueryBuilder('p')
            ->where('p.provider = :provider')
            ->andWhere('p.model = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('provider', $provider)
            ->setParameter('model', $model)
            ->setParameter('ts', $ts)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
