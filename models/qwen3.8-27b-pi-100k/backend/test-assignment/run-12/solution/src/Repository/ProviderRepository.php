<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Provider;
use Doctrine\ORM\EntityManagerInterface;

final class ProviderRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function upsert(string $code, string $name, ?string $startHM, ?string $endHM, ?string $multiplier): void
    {
        $entity = $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]);
        if ($entity === null) {
            $entity = new Provider();
            $entity->code = $code;
            $this->em->persist($entity);
        }
        $entity->name = $name;
        $entity->offPeakStart = $startHM;
        $entity->offPeakEnd = $endHM;
        $entity->offPeakMultiplier = $multiplier;
    }

    /** @return array<string, array{name:string, start:string|null, end:string|null, multiplier:string|null}> ключ — код */
    public function allAsArray(): array
    {
        $result = [];
        foreach ($this->em->getRepository(Provider::class)->findAll() as $p) {
            $result[$p->code] = [
                'name' => $p->name,
                'start' => $p->offPeakStart,
                'end' => $p->offPeakEnd,
                'multiplier' => $p->offPeakMultiplier,
            ];
        }

        return $result;
    }

    public function find(string $code): ?array
    {
        $p = $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]);
        if ($p === null) {
            return null;
        }

        return [
            'name' => $p->name,
            'start' => $p->offPeakStart,
            'end' => $p->offPeakEnd,
            'multiplier' => $p->offPeakMultiplier,
        ];
    }
}
