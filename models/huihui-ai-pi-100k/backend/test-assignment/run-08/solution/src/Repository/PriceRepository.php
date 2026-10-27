<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Price;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepositoryInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Price> */
final class PriceRepository extends ServiceEntityRepository implements ServiceEntityRepositoryInterface
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Price::class);
    }
    /**
     * Выбор строки цены события:
     * самая поздняя, с valid_from ≤ ts (valid_from включительно).
     *
     * @throws NonUniqueResultException
     */
    public function findPriceFor(\App\Entity\UsageEvent $event): ?Price
    {
        try {
            return $this->createQueryBuilder('p')
                ->where('p.providerCode = :provider')
                ->andWhere('p.modelCode = :model')
                ->andWhere('p.validFrom <= :ts')
                ->orderBy('p.validFrom', 'DESC')
                ->setParameter('provider', $event->providerCode)
                ->setParameter('model', $event->modelCode)
                ->setParameter('ts', $event->ts)
                ->setMaxResults(1)
                ->getQuery()
                ->getOneOrNullResult();
        } catch (\Doctrine\ORM\Query\NoResultException) {
            return null;
        }
    }

    public function findByRow(string $provider, string $model, \DateTimeImmutable $validFrom): ?Price
    {
        return $this->findOneBy([
            'providerCode' => $provider,
            'modelCode' => $model,
            'validFrom' => $validFrom,
        ]);
    }
}
