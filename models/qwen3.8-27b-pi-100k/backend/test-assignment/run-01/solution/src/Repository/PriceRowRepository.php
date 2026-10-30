<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Model;
use App\Entity\PriceRow;
use DateTimeImmutable;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<PriceRow> */
final class PriceRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceRow::class);
    }

    /**
     * Строка цен с наибольшим valid_from ≤ $tsUtc (valid_from включительно),
     * либо null, если такой строки нет.
     */
    public function findActiveAt(string $providerCode, string $modelCode, DateTimeImmutable $tsUtc): ?PriceRow
    {
        $tsUtc = $tsUtc->setTimezone(new \DateTimeZone('UTC'));

        return $this->createQueryBuilder('p')
            ->innerJoin(Model::class, 'm', 'WITH', 'p.model = m')
            ->andWhere('m.providerCode = :provider')
            ->andWhere('m.code = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('provider', $providerCode)
            ->setParameter('model', $modelCode)
            ->setParameter('ts', $tsUtc)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findByProviderModelAndValidFrom(string $providerCode, string $modelCode, DateTimeImmutable $validFromUtc): ?PriceRow
    {
        $validFromUtc = $validFromUtc->setTimezone(new \DateTimeZone('UTC'));

        return $this->createQueryBuilder('p')
            ->innerJoin(Model::class, 'm', 'WITH', 'p.model = m')
            ->andWhere('m.providerCode = :provider')
            ->andWhere('m.code = :model')
            ->andWhere('p.validFrom = :validFrom')
            ->setParameter('provider', $providerCode)
            ->setParameter('model', $modelCode)
            ->setParameter('validFrom', $validFromUtc)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
