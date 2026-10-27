<?php

declare(strict_types=1);

namespace App\Application\Prices;

use App\Domain\Model\Model;
use App\Domain\Provider\Provider;
use App\Domain\Price\PriceRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PriceRow>
 */
class PriceRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceRow::class);
    }

    /**
     * Пункт 1: строка цен — наибольшее valid_from <= $ts.
     *
     * @return PriceRow|null
     */
    public function pickApplicable(Provider $provider, Model $model, \DateTimeImmutable $ts): ?PriceRow
    {
        $q = $this->createQueryBuilder('p')
            ->where('p.provider = :prov')
            ->andWhere('p.model = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('prov', $provider)
            ->setParameter('model', $model)
            ->setParameter('ts', $ts)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        return $q->getOneOrNullResult();
    }

    public function findForRerate(Provider $provider, Model $model, \DateTimeImmutable $validFrom): ?PriceRow
    {
        return $this->findOneBy(['provider' => $provider, 'model' => $model, 'validFrom' => $validFrom]);
    }
}
