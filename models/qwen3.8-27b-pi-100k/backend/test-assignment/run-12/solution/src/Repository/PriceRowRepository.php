<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\PriceRow;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

final class PriceRowRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * Строка с наибольшим valid_from ≤ ts для (provider, model) — п. 1 задания.
     */
    public function findLatestUpTo(string $provider, string $model, DateTimeImmutable $ts): ?PriceRow
    {
        return $this->em->getRepository(PriceRow::class)
            ->createQueryBuilder('p')
            ->where('p.provider = :provider')
            ->andWhere('p.model = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('provider', $provider)
            ->setParameter('model', $model)
            ->setParameter('ts', $ts)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @param array<string,string> $rates */
    public function upsert(
        string $provider,
        string $model,
        DateTimeImmutable $validFrom,
        string $unit,
        array $rates,
    ): PriceRow {
        $row = $this->em->getRepository(PriceRow::class)
            ->findOneBy(['provider' => $provider, 'model' => $model, 'validFrom' => $validFrom]);

        if ($row !== null) {
            $row->unit = $unit;
            $row->rateInput = $rates['input'] ?? null;
            $row->rateOutput = $rates['output'] ?? null;
            $row->rateCacheRead = $rates['cache_read'] ?? null;
            $row->rateCacheWrite = $rates['cache_write'] ?? null;
            $row->rateAny = ($unit === 'per_1m_tokens_unified') ? ($rates['any'] ?? null) : null;

            return $row;
        }

        $row = new PriceRow();
        $row->provider = $provider;
        $row->model = $model;
        $row->validFrom = $validFrom;
        $row->unit = $unit;
        $row->rateInput = $rates['input'] ?? null;
        $row->rateOutput = $rates['output'] ?? null;
        $row->rateCacheRead = $rates['cache_read'] ?? null;
        $row->rateCacheWrite = $rates['cache_write'] ?? null;
        $row->rateAny = ($unit === 'per_1m_tokens_unified') ? ($rates['any'] ?? null) : null;
        $this->em->persist($row);

        return $row;
    }
}
