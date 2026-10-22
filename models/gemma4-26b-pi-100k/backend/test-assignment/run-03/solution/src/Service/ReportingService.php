<?php

namespace App\Service;

use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

class ReportingService
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    public function generateReport(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $queryBuilder = $this->entityManager->createQueryBuilder();
        $queryBuilder->select('u.user, u.provider, u.modelCode, SUM(u.tokensInput) as totalInput, SUM(u.tokensOutput) as totalOutput, SUM(u.tokensCacheRead) as totalCacheRead, SUM(u.tokensCacheWrite) as totalCacheWrite, SUM(u.costUsd) as totalCost')
            ->from(UsageEvent::class, 'u')
            ->where('u.isRated = true')
            ->groupBy('u.user, u.provider, u.modelCode')
            ->orderBy('u.user', 'ASC')
            ->addOrderBy('u.provider', 'ASC')
            ->addOrderBy('u.modelCode', 'ASC');

        if ($from) {
            $queryBuilder->andWhere('u.ts >= :from')->setParameter('from', $from);
        }
        if ($to) {
            $queryBuilder->andWhere('u.ts < :to')->setParameter('to', $to);
        }

        $results = $queryBuilder->getQuery()->getResult();

        return $results;
    }
}
