<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Model;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Model>
 */
final class ModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Model::class);
    }

    public function save(Model $model): void
    {
        $this->getEntityManager()->persist($model);
        $this->getEntityManager()->flush();
    }

    public function findByCode(string $providerCode, string $modelCode): ?Model
    {
        /** @var Model|null $model */
        return $this->createQueryBuilder('m')
            ->innerJoin('m.provider', 'p')
            ->where('m.code = :code')
            ->andWhere('p.code = :providerCode')
            ->setParameter('code', $modelCode)
            ->setParameter('providerCode', $providerCode)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
