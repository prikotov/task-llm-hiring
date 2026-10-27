<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Model;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<Model>
 */
class ModelRepository extends ServiceEntityRepository
{
    public function findByCode(string $providerCode, string $code): ?Model
    {
        return $this->findOneBy(['providerCode' => $providerCode, 'code' => $code]);
    }
}
