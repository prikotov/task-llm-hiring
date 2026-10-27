<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Price;
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

    public function findByPKey(string $providerCode, string $modelCode, \DateTimeImmutable $validFrom): ?Price
    {
        return $this->findOneBy([
            'providerCode' => $providerCode,
            'modelCode' => $modelCode,
            'validFrom' => $validFrom,
        ]);
    }

    /**
     * Строка цен с наибольшим valid_from <= asOf для данной пары (provider, model).
     * Возвращает null, если строки нет.
     */
    public function findPricedAt(string $providerCode, string $modelCode, \DateTimeImmutable $asOf): ?Price
    {
        $rows = $this->createQueryBuilder('p')
            ->where('p.providerCode = :pc')
            ->andWhere('p.modelCode = :mc')
            ->andWhere('p.validFrom <= :asOf')
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->setParameter('pc', $providerCode)
            ->setParameter('mc', $modelCode)
            ->setParameter('asOf', $asOf)
            ->getQuery()
            ->getResult();

        return $rows[0] ?? null;
    }

    public function allWithProviderAndModel(): array
    {
        return $this->findAll();
    }
}
