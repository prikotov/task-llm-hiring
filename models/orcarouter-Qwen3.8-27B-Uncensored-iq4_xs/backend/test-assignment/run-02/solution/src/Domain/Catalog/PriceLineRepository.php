<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

use function count;

/** @extends ServiceEntityRepository<PriceLine> */
final class PriceLineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceLine::class);
    }

    public function findPriceLine(Model $model, \DateTimeImmutable $validFrom): ?PriceLine
    {
        return $this->createQueryBuilder('l')
            ->where('l.model = :model')
            ->andWhere('l.validFrom = :validFrom')
            ->setParameter('model', $model)
            ->setParameter('validFrom', $validFrom)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Строка цен, действовавшая в момент $ts: наибольшее valid_from <= ts
     * для модели (provider, modelCode). null — ставки неизвестны (unrated).
     */
    public function findApplicableLine(string $providerCode, string $modelCode, \DateTimeImmutable $ts): ?PriceLine
    {
        $lines = $this->createQueryBuilder('l')
            ->join('l.provider', 'p')
            ->join('l.model', 'm')
            ->where('p.code = :provider')
            ->andWhere('m.code = :model')
            ->andWhere('m.provider = p')
            ->andWhere('l.validFrom <= :ts')
            ->orderBy('l.validFrom', 'DESC')
            ->setParameter('provider', $providerCode)
            ->setParameter('model', $modelCode)
            ->setParameter('ts', $ts)
            ->setMaxResults(1)
            ->getQuery()
            ->getResult();

        return count($lines) > 0 ? $lines[0] : null;
    }
}
