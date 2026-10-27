<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Provider>
 */
class ProviderRepository extends ServiceEntityRepository
{
    public function findByCode(string $code): ?Provider
    {
        return $this->findOneBy(['code' => $code]);
    }
}
