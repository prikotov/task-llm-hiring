<?php

declare(strict_types=1);

namespace App\Billing\Infrastructure\Persistence;

use App\Billing\Domain\Catalog\Model;
use App\Billing\Domain\Catalog\Price;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Price> */
final class PriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Price::class);
    }

    /** Строка цен, действующая в момент $ts: наибольший valid_from <= $ts. */
    public function findActiveFor(Model $model, DateTimeImmutable $ts): ?Price
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.model = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('model', $model)
            ->setParameter('ts', $ts)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByKey(Model $model, DateTimeImmutable $validFrom): ?Price
    {
        return $this->findOneBy(['model' => $model, 'validFrom' => $validFrom]);
    }
}
