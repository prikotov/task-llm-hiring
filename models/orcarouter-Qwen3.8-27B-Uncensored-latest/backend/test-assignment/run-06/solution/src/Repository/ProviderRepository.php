<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class ProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Provider::class);
    }

    public function findByCode(string $code): ?Provider
    {
        return $this->findOneBy(['code' => $code]);
    }

    /** @return Provider[] */
    public function findAllActive(): array
    {
        return $this->findBy([], ['code' => 'ASC']);
    }
}
