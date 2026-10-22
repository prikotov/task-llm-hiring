<?php

declare(strict_types=1);

namespace App\Domain\Service;

use App\Domain\Model\PriceRow;
use App\Domain\Model\Provider;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Справочник: провайдеры и строки цен.
 */
final class PriceBook
{
    /** @param object|class-string $entityClass */
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function provider(string $code): ?Provider
    {
        /** @var Provider|null $p */
        $p = $this->em->getRepository(Provider::class)->find($code);

        return $p;
    }

    public function hasProvider(string $code): bool
    {
        return null !== $this->provider($code);
    }

    /**
     * Строка цен с наибольшим valid_from, не превосходящим $at (содержит в
     * себе).
     *
     * @return PriceRow|null
     */
    public function findPrice(string $provider, string $model, \DateTimeInterface $at): ?PriceRow
    {
        $qb = $this->em->createQueryBuilder();
        $qb->select('p')
            ->from(PriceRow::class, 'p')
            ->where('p.providerCode = :provider')
            ->andWhere('p.modelCode = :model')
            ->andWhere('p.validFrom <= :at')
            ->setParameter('provider', $provider)
            ->setParameter('model', $model)
            ->setParameter('at', $at)
            ->orderBy('p.validFrom', 'DESC');

        /** @var PriceRow|null $row */
        $row = $qb->getSingleResultOrNull();

        return $row;
    }
}
