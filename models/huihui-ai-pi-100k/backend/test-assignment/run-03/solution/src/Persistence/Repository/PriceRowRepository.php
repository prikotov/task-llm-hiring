<?php

declare(strict_types=1);

namespace App\Persistence\Repository;

use App\Domain\Common\Iso8601Utc as Utc;
use App\Persistence\Entity\PriceRow;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

final class PriceRowRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * Все строки цен пары (провайдер, модель), по возрастанию valid_from
     * (последняя — действовала позже всех).
     *
     * @return list<PriceRow>
     */
    public function findAllFor(string $providerCode, string $modelCode): array
    {
        return $this->em->createQueryBuilder()
            ->select('pr')
            ->from(PriceRow::class, 'pr')
            ->join('pr.provider', 'p')
            ->join('pr.model', 'm')
            ->where('p.code = :provider')
            ->andWhere('m.code = :model')
            ->setParameter('provider', $providerCode)
            ->setParameter('model', $modelCode)
            ->orderBy('pr.validFrom', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function findFor(string $providerCode, string $modelCode, \DateTimeImmutable $validFrom): ?PriceRow
    {
        return $this->em->createQueryBuilder()
            ->select('pr')
            ->from(PriceRow::class, 'pr')
            ->join('pr.provider', 'p')
            ->join('pr.model', 'm')
            ->where('p.code = :provider')
            ->andWhere('m.code = :model')
            ->andWhere('pr.validFrom = :validFrom')
            ->setParameter('provider', $providerCode)
            ->setParameter('model', $modelCode)
            ->setParameter('validFrom', Utc::toMutableUtc($validFrom))
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return list<PriceRow> */
    public function findByIds(array $ids): array
    {
        if ($ids === []) {
            return [];
        }

        return $this->em->createQueryBuilder()
            ->select('pr')
            ->from(PriceRow::class, 'pr')
            ->where('pr.id IN (:ids)')
            ->setParameter('ids', array_values($ids))
            ->getQuery()
            ->getResult(Query::HYDRATE_OBJECT);
    }
}
