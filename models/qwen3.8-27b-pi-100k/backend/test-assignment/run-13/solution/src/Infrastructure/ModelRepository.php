<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<Model>
 */
final class ModelRepository extends EntityRepository
{
    public function findByKeys(string $provider, string $code): ?Model
    {
        return $this->findOneBy(['providerCode' => $provider, 'code' => $code]);
    }
}
