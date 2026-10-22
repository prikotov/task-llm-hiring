<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Model> */
final class ModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Model::class);
    }

    public function findModelByCode(Provider $provider, string $code): ?Model
    {
        return $this->createQueryBuilder('m')
            ->where('m.provider = :provider')
            ->andWhere('m.code = :code')
            ->setParameter('provider', $provider)
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
