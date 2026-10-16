<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Model;
use App\Entity\Price;
use Doctrine\ORM\EntityManagerInterface;

final class PriceRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    public function findByModelAndValidFrom(Model $model, \DateTimeInterface $validFrom): ?Price
    {
        return $this->em->getRepository(Price::class)->findOneBy([
            'model' => $model,
            'validFrom' => $validFrom,
        ]);
    }

    /**
     * Строка цен, действующая в момент $ts:
     * наибольшее valid_from среди valid_from <= $ts (valid_from включительно).
     */
    public function latestValidAt(Model $model, \DateTimeInterface $ts): ?Price
    {
        $query = $this->em->createQueryBuilder()
            ->select('p')
            ->from(Price::class, 'p')
            ->where('p.model = :model')
            ->andWhere('p.validFrom <= :ts')
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery();
        $query->setParameter('model', $model);
        $query->setParameter('ts', $ts);

        return $query->getOneOrNullResult();
    }
}
