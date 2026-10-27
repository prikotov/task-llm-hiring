<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Doctrine\ORM\EntityRepository;

/**
 * @extends EntityRepository<UsageEvent>
 */
final class UsageEventRepository extends EntityRepository
{
    /**
     * @return UsageEvent[]
     */
    public function findUnrated(): array
    {
        /** @var UsageEvent[] $result */
        $result = $this->createQueryBuilder('e')
            ->where('e.status = :status')
            ->setParameter('status', UsageEvent::STATUS_UNRATED)
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();

        return $result;
    }

    public function findByNaturalKey(string $provider, string $requestId): ?UsageEvent
    {
        return $this->findOneBy(['provider' => $provider, 'requestId' => $requestId]);
    }
}
