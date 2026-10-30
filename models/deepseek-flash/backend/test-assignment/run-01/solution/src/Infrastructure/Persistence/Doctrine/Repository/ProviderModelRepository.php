<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Repository;

use App\Infrastructure\Persistence\Doctrine\Entity\Provider;
use App\Infrastructure\Persistence\Doctrine\Entity\ProviderModel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProviderModel>
 */
final class ProviderModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProviderModel::class);
    }

    public function findByProviderAndCode(Provider $provider, string $code): ?ProviderModel
    {
        return $this->findOneBy(['provider' => $provider, 'code' => $code]);
    }
}
