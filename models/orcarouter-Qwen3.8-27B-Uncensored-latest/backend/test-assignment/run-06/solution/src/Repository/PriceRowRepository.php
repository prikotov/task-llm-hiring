<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PriceRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

final class PriceRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceRow::class);
    }

    public function findByRef(string $provider, string $model, \DateTimeImmutable $validFrom): ?PriceRow
    {
        return $this->findOneBy(['provider' => $provider, 'model' => $model, 'validFrom' => $validFrom]);
    }

    /**
     * Все строки цен модели для подбора подходящей под событие.
     *
     * @return PriceRow[]
     */
    public function findByModel(string $provider, string $model): array
    {
        return $this->findBy(['provider' => $provider, 'model' => $model], ['validFrom' => 'DESC']);
    }

    /** @return PriceRow[] */
    public function findByRefs(array $refs): array
    {
        if ($refs === []) {
            return [];
        }

        $q = $this->createQueryBuilder('p');
        $q->andWhere('p.provider IN (:providers)')
            ->setParameter('providers', array_column($refs, 0));
        $conds = [];
        $params = [];
        foreach ($refs as $i => [$provider, $model, $validFrom]) {
            $conds[] = sprintf('(p.provider = :p_%d AND p.model = :m_%d AND p.validFrom = :f_%d)', $i, $i, $i);
            $params["p_{$i}"] = $provider;
            $params["m_{$i}"] = $model;
            $params["f_{$i}"] = $validFrom;
        }
        $q->andWhere(implode(' OR ', $conds));
        foreach ($params as $k => $v) {
            $q->setParameter($k, $v);
        }

        return $q->getQuery()->getResult();
    }
}
