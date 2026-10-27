<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<Provider>
 */
final class ProviderRepository extends EntityRepository
{
    public function findByCode(string $code): ?Provider
    {
        return $this->findOneBy(['code' => $code]);
    }
}
