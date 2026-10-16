<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Model;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Model>
 */
class ModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Model::class);
    }

    public function findByProviderCodeAndCode(string $providerCode, string $code): ?Model
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.provider', 'p')
            ->where('p.code = :providerCode')
            ->andWhere('m.code = :code')
            ->setParameter('providerCode', $providerCode)
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
