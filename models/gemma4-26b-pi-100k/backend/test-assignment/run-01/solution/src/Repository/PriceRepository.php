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
     * Finds the most recent price for a model that is valid at the given timestamp.
     * 
     * @param \App\Entity\Model $model
     * @param \DateTimeImmutable $ts
     * @return Price|null
     */
    public function findPriceForModelAtTime(\App\Entity\Model $model, \DateTimeImmutable $ts): ?Price
    {
        return $this->createQueryBuilder('p')
            ->where('p.model = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('model', $model)
            ->setParameter('ts', $ts)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
