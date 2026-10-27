<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\UsageEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @extends ServiceEntityRepository<UsageEvent>
 */
class UsageEventRepository extends ServiceEntityRepository
{
    public function findByNaturalKey(string $providerCode, string $providerRequestId): ?UsageEvent
    {
        return $this->findOneBy(['providerCode' => $providerCode, 'providerRequestId' => $providerRequestId]);
    }

    /** @return UsageEvent[] */
    public function findPending(): array
    {
        return $this->findBy(['status' => UsageEvent::STATUS_PENDING], ['id' => 'ASC']);
    }

    /** @return UsageEvent[] */
    public function findUnrated(): array
    {
        return $this->findBy(['status' => UsageEvent::STATUS_UNRATED], ['id' => 'ASC']);
    }

    /** @return UsageEvent[] */
    public function findRatedByPriceLine(string $providerCode, string $modelCode, \DateTimeImmutable $validFrom): array
    {
        $qb = $this->createQueryBuilder('e');
        $qb->where('e.status = :status')
            ->andWhere('e.ratedProvider = :pc')
            ->andWhere('e.ratedModel = :mc')
            ->andWhere('e.ratedPriceValidFrom = :vf')
            ->setParameter('status', UsageEvent::STATUS_RATED)
            ->setParameter('pc', $providerCode)
            ->setParameter('mc', $modelCode)
            ->setParameter('vf', $validFrom);
        return $qb->getQuery()->getResult();
    }

    /**
     * Report aggregate: one row per (user, provider/model) group.
     *
     * @return array<int, array{user:string,pc:string,mc:string,tin:int,tout:int,tcr:int,tcw:int,total:string}>
     */
    public function aggregateReport(?string $fromIso, ?string $toIso): array
    {
        $qb = $this->createQueryBuilder('e');
        $qb->select(
            'e.userName AS user',
            'e.ratedProvider AS pc',
            'e.ratedModel AS mc',
            'SUM(e.tokensInput) AS tin',
            'SUM(e.tokensOutput) AS tout',
            'SUM(e.tokensCacheRead) AS tcr',
            'SUM(e.tokensCacheWrite) AS tcw'
        )
            ->where('e.status = :status')
            ->setParameter('status', UsageEvent::STATUS_RATED)
            ->groupBy('e.userName, e.ratedProvider, e.ratedModel');
        if ($fromIso !== null) {
            $qb->andWhere('e.tsUtc >= :from')->setParameter('from', new \DateTimeImmutable($fromIso));
        }
        if ($toIso !== null) {
            $qb->andWhere('e.tsUtc < :to')->setParameter('to', new \DateTimeImmutable($toIso));
        }
        $rows = $qb->getQuery()->getScalarResult();

        // Sum cost per group in PHP (cost_usd are exact decimal strings).
        $groups = [];
        foreach ($rows as $r) {
            $key = $r['user'] . "\x00" . $r['pc'] . '/' . $r['mc'];
            $groups[$key] = [
                'user' => $r['user'],
                'pc' => $r['pc'],
                'mc' => $r['mc'],
                'tin' => (int) $r['tin'],
                'tout' => (int) $r['tout'],
                'tcr' => (int) $r['tcr'],
                'tcw' => (int) $r['tcw'],
            ];
        }

        // Sum cost per (user, model) group in PHP.
        $costQb = $this->createQueryBuilder('e');
        $costQb->select('e.userName AS user', 'e.ratedProvider AS pc', 'e.ratedModel AS mc', 'e.costUsd AS cost')
            ->where('e.status = :status')
            ->setParameter('status', UsageEvent::STATUS_RATED);
        if ($fromIso !== null) {
            $costQb->andWhere('e.tsUtc >= :from')->setParameter('from', new \DateTimeImmutable($fromIso));
        }
        if ($toIso !== null) {
            $costQb->andWhere('e.tsUtc < :to')->setParameter('to', new \DateTimeImmutable($toIso));
        }
        $costRows = $costQb->getQuery()->getScalarResult();
        $costs = [];
        foreach ($costRows as $r) {
            $key = $r['user'] . "\x00" . $r['pc'] . '/' . $r['mc'];
            $costs[$key] = bcadd($costs[$key] ?? '0', (string) $r['cost'], 20);
        }

        // Merge and sort
        $out = [];
        foreach ($groups as $key => $g) {
            $g['total'] = bcadd($costs[$key] ?? '0', '0', 20);
            $g['model'] = $g['pc'] . '/' . $g['mc'];
            unset($g['pc'], $g['mc']);
            $out[$key] = $g;
        }
        ksort($out);
        return $out;
    }
}
