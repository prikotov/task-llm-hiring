<?php

namespace App\Repository;

use App\Entity\Price;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Price>
 */
class PriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Price::class);
    }

    /**
     * @return Price|null
     */
    public function findLatestPriceForModel(\DateTimeImmutable $ts, string $providerCode, int $modelId): ?Price
    {
        return $this->createQueryBuilder('p')
            ->where('p.provider = :provider')
            ->andWhere('p.model = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('provider', $providerCode)
            ->setParameter('model', $modelId)
            ->setParameter('ts', $ts)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
