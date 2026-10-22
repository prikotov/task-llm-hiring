<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Provider>
 */
final class ProviderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Provider::class);
    }

    public function save(Provider $provider): void
    {
        $this->getEntityManager()->persist($provider);
        $this->getEntityManager()->flush();
    }

    public function findOneByCode(string $code): ?Provider
    {
        /** @var Provider|null $provider */
        return $this->findOneBy(['code' => $code]);
    }
}
