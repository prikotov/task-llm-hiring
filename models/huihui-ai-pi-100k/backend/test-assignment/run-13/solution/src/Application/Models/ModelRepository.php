<?php

declare(strict_types=1);

namespace App\Application\Models;

use App\Domain\Model\Model;
use App\Domain\Provider\Provider;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Model>
 */
class ModelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Model::class);
    }

    public function findOrCreate(Provider $provider, string $code): Model
    {
        $existing = $this->findOneBy(['provider' => $provider, 'code' => $code]);
        if ($existing !== null) {
            return $existing;
        }
        $m = new Model($provider, $code);
        $this->getEntityManager()->persist($m);

        return $m;
    }
}
