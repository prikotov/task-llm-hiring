<?php

declare(strict_types=1);

namespace App\Application\Providers;

use App\Domain\Provider\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Provider>
 */
class ProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Provider::class);
    }

    public function findOneByCode(string $code): ?Provider
    {
        return $this->findOneBy(['code' => $code]);
    }
}
