<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\PriceLine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<PriceLine>
 */
class PriceLineRepository extends ServiceEntityRepository
{
    public function findByKey(string $providerCode, string $modelCode, \DateTimeImmutable $validFrom): ?PriceLine
    {
        return $this->findOneBy([
            'providerCode' => $providerCode,
            'modelCode' => $modelCode,
            'validFrom' => $validFrom,
        ]);
    }

    /**
     * Pick the price line for an event at $ts:
     * greatest validFrom <= $ts.
     */
    public function pickForInstant(string $providerCode, string $modelCode, \DateTimeImmutable $ts): ?PriceLine
    {
        $qb = $this->createQueryBuilder('p');
        $qb->where('p.providerCode = :pc')
            ->andWhere('p.modelCode = :mc')
            ->andWhere('p.validFrom <= :ts')
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->setParameter('pc', $providerCode)
            ->setParameter('mc', $modelCode)
            ->setParameter('ts', $ts);
        $rows = $qb->getQuery()->getResult();
        return $rows[0] ?? null;
    }
}
