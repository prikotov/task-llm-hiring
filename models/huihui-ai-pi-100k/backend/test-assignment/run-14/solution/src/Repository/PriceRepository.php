<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Model;
use App\Entity\Price;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Строки цен моделей (ключ — (model, validFrom)).
 */
final class PriceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Price::class);
    }

    public function byModelAndValidFrom(Model $model, \DateTimeInterface $validFrom): ?Price
    {
        return $this->findOneBy(['model' => $model, 'validFrom' => $validFrom]);
    }

    /**
     * Строка цен модели, действующая в момент $when — наибольший `validFrom`
     * (UTC), не превышающий $when (включительно). null — unrated.
     */
    public function applicablePrice(Model $model, \DateTimeInterface $when): ?Price
    {
        $whenUtc = \DateTimeImmutable::createFromInterface($when)->setTimezone(new \DateTimeZone('UTC'));

        return $this->createQueryBuilder('p')
            ->where('p.model = :m')
            ->andWhere('p.validFrom <= :when')
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->setParameter('m', $model)
            ->setParameter('when', $whenUtc)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
