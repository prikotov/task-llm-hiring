<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Catalog\PriceRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PriceRow>
 */
class PriceRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceRow::class);
    }

    /**
     * Строка цены, действующая для модели на момент $at:
     * наибольшее valid_from <= $at (valid_from включительно; правила п. 1).
     */
    public function findEffectiveForModel(string $providerCode, string $modelCode, \DateTimeImmutable $at): ?PriceRow
    {
        return $this->createQueryBuilder('p')
            ->join('p.model', 'm')
            ->andWhere('m.providerCode = :provider')
            ->andWhere('m.code = :model')
            ->andWhere('p.validFrom <= :at')
            ->orderBy('p.validFrom', 'DESC')
            ->addOrderBy('p.id', 'DESC')
            ->setParameter('provider', $providerCode)
            ->setParameter('model', $modelCode)
            ->setParameter('at', $at)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findOneByModelAndValidFrom(string $providerCode, string $modelCode, \DateTimeImmutable $validFrom): ?PriceRow
    {
        return $this->createQueryBuilder('p')
            ->join('p.model', 'm')
            ->andWhere('m.providerCode = :provider')
            ->andWhere('m.code = :model')
            ->andWhere('p.validFrom = :validFrom')
            ->setParameter('provider', $providerCode)
            ->setParameter('model', $modelCode)
            ->setParameter('validFrom', $validFrom)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
