<?php

declare(strict_types=1);

namespace App\Billing\Infrastructure\Persistence;

use App\Billing\Domain\Catalog\Model;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<Model> */
final class ModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Model::class);
    }

    public function findOneByKey(string $providerCode, string $code): ?Model
    {
        return $this->createQueryBuilder('m')
            ->join('m.provider', 'p')
            ->andWhere('p.code = :provider')
            ->andWhere('m.code = :code')
            ->setParameter('provider', $providerCode)
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
