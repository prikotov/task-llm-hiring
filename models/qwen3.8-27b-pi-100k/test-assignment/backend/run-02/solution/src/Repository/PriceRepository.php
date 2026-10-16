<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Model;
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

    public function findByModelAndValidFrom(Model $model, \DateTimeImmutable $validFrom): ?Price
    {
        return $this->createQueryBuilder('p')
            ->where('p.model = :model')
            ->andWhere('p.validFrom = :validFrom')
            ->setParameter('model', $model)
            ->setParameter('validFrom', $validFrom)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Все строки цен модели, по возрастанию valid_from.
     *
     * @return Price[]
     */
    public function findByModel(Model $model): array
    {
        return $this->createQueryBuilder('p')
            ->where('p.model = :model')
            ->orderBy('p.validFrom', 'ASC')
            ->setParameter('model', $model)
            ->getQuery()
            ->getResult();
    }
}
