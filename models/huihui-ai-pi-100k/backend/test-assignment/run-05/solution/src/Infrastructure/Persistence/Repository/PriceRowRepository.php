<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Repository;

use App\Infrastructure\Persistence\Entity\PriceRow;
use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<PriceRow>
 */
final class PriceRowRepository extends EntityRepository
{
    /**
     * Строка цен своей модели с наибольшим validFrom <= $time
     * (validFrom включительно); null, если такой строки нет.
     */
    public function latestAtOrBefore(string $provider, string $model, \DateTimeImmutable $time): ?PriceRow
    {
        return $this->createQueryBuilder('p')
            ->where('p.provider = :provider')
            ->andWhere('p.model = :model')
            ->andWhere('p.validFrom <= :time')
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->setParameter('provider', $provider)
            ->setParameter('model', $model)
            ->setParameter('time', $time)
            ->getOneOrNullResult();
    }

    public function findByNaturalKey(string $provider, string $model, \DateTimeImmutable $validFrom): ?PriceRow
    {
        return $this->findOneBy([
            'provider' => $provider,
            'model' => $model,
            'validFrom' => $validFrom,
        ]);
    }
}
