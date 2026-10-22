<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Repository;

use App\Domain\Pricing\Provider;
use App\Infrastructure\Doctrine\Entity\ProviderEntity;
use Doctrine\ORM\EntityManagerInterface;

final class ProviderRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function findByCode(string $code): ?ProviderEntity
    {
        return $this->em->getRepository(ProviderEntity::class)->findOneBy(['code' => $code]);
    }

    /**
     * Идемпотентный upsert по коду: обновляет имя и оф-пик.
     */
    public function upsert(Provider $provider): ProviderEntity
    {
        $entity = $this->findByCode($provider->code);

        if (null === $entity) {
            $entity = ProviderEntity::fromDomain($provider);
            $this->em->persist($entity);

            return $entity;
        }

        $entity->applyFromDomain($provider);

        return $entity;
    }
}
