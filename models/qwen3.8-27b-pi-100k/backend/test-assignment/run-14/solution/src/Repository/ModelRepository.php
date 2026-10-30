<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Model;
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

    public function findByProviderAndCode(string $providerCode, string $modelCode): ?Model
    {
        return $this->findOneBy(['providerCode' => $providerCode, 'code' => $modelCode]);
    }

    public function allByProvider(string $providerCode): array
    {
        return $this->findBy(['providerCode' => $providerCode]);
    }
}
