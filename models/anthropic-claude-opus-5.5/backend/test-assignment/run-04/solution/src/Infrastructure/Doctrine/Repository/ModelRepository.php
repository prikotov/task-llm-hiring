<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Infrastructure\Doctrine\Entity\ModelEntity;
use App\Infrastructure\Doctrine\Entity\ProviderEntity;
use Doctrine\ORM\EntityManagerInterface;

final class ModelRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function findOne(ProviderEntity $provider, string $code): ?ModelEntity
    {
        return $this->em->getRepository(ModelEntity::class)->findOneBy([
            'provider' => $provider,
            'code' => $code,
        ]);
    }

    public function upsert(ProviderEntity $provider, string $code): ModelEntity
    {
        $entity = $this->findOne($provider, $code);
        if (null !== $entity) {
            return $entity;
        }

        $entity = new ModelEntity($provider, $code);
        $this->em->persist($entity);

        return $entity;
    }
}
