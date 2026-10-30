<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Model;
use Doctrine\ORM\EntityManagerInterface;

final class ModelRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function upsert(string $provider, string $code): void
    {
        $entity = $this->em->getRepository(Model::class)->findOneBy(['provider' => $provider, 'code' => $code]);
        if ($entity === null) {
            $entity = new Model();
            $entity->provider = $provider;
            $entity->code = $code;
            $this->em->persist($entity);
        }
    }
}
