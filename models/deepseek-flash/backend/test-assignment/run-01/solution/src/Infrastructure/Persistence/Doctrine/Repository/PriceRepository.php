<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Infrastructure\Persistence\Doctrine\Entity\Price;
use App\Infrastructure\Persistence\Doctrine\Entity\ProviderModel;
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

    public function findForModelAt(ProviderModel $model, DateTimeImmutable $validFrom): ?Price
    {
        return $this->findOneBy(['model' => $model, 'validFrom' => $validFrom]);
    }

    public function findApplicableForModel(ProviderModel $model, DateTimeImmutable $moment): ?Price
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.model = :model')
            ->andWhere('p.validFrom <= :moment')
            ->setParameter('model', $model)
            ->setParameter('moment', $moment)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findNextForModelAfter(ProviderModel $model, DateTimeImmutable $after): ?Price
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.model = :model')
            ->andWhere('p.validFrom > :after')
            ->setParameter('model', $model)
            ->setParameter('after', $after)
            ->orderBy('p.validFrom', 'ASC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
