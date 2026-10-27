<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\Model;
use App\Entity\PriceRow;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
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

    public function findForModelAt(string $providerCode, string $modelCode, \DateTimeImmutable $when): ?PriceRow
    {
        $query = $this->createQueryBuilder('pr')
            ->join('pr.provider', 'p')
            ->join('pr.model', 'm')
            ->where('p.code = :provider')
            ->andWhere('m.code = :model')
            ->andWhere('pr.validFrom <= :when')
            ->orderBy('pr.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery();

        $query->setParameter('provider', $providerCode);
        $query->setParameter('model', $modelCode);
        $query->setParameter('when', $when);

        return $query->getOneOrNullResult();
    }

    /**
     * @return iterable<PriceRow>
     */
    public function forModel(Model $model): iterable
    {
        return $this->findBy(['model' => $model], ['validFrom' => 'ASC']);
    }

    /**
     * Строка по естественному ключу (provider, model, valid_from).
     */
    public function findByKey(Model $model, \DateTimeImmutable $validFrom): ?PriceRow
    {
        return $this->findOneBy(['model' => $model, 'validFrom' => $validFrom]);
    }
}
