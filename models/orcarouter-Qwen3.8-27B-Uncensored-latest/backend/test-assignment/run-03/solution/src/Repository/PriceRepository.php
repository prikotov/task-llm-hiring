<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Price;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Price>
 */
final class PriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Price::class);
    }

    /**
     * Строка цен, действующая в момент $ts: максимальный valid_from <= $ts
     * (valid_from включительно). null, если такой строки нет.
     */
    public function findApplicable(string $providerCode, string $modelCode, DateTimeImmutable $ts): ?Price
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.provider', 'pr', 'WITH', 'pr.code = :pc')
            ->innerJoin('p.model', 'm', 'WITH', 'm.code = :mc')
            ->andWhere('p.validFrom <= :ts')
            ->orderBy('p.validFrom', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->setMaxResults(1)
            ->setParameter('pc', $providerCode)
            ->setParameter('mc', $modelCode)
            ->setParameter('ts', $ts)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Строка цен по естественному ключу (provider code, model code, valid_from).
     */
    public function findByKey(string $providerCode, string $modelCode, DateTimeImmutable $validFrom): ?Price
    {
        return $this->createQueryBuilder('p')
            ->innerJoin('p.provider', 'pr', 'WITH', 'pr.code = :pc')
            ->innerJoin('p.model', 'm', 'WITH', 'm.code = :mc')
            ->andWhere('p.validFrom = :vf')
            ->setParameter('pc', $providerCode)
            ->setParameter('mc', $modelCode)
            ->setParameter('vf', $validFrom)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
