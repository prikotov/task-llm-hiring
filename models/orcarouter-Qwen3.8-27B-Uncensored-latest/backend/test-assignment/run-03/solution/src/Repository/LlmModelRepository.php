<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\LlmModel;
use App\Entity\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<LlmModel>
 */
final class LlmModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, LlmModel::class);
    }

    public function findByProviderAndCode(Provider $provider, string $code): ?LlmModel
    {
        return $this->findOneBy(['provider' => $provider, 'code' => $code]);
    }
}
