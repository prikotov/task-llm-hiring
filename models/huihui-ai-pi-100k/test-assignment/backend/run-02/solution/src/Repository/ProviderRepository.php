<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;

final class ProviderRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function findByCode(string $code): ?Provider
    {
        return $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]);
    }
}
