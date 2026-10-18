<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UsageEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/** @extends ServiceEntityRepository<UsageEvent> */
final class UsageEventRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UsageEvent::class);
    }

    public function findByProviderAndRequestId(string $providerCode, string $providerRequestId): ?UsageEvent
    {
        return $this->findOneBy(['providerCode' => $providerCode, 'providerRequestId' => $providerRequestId]);
    }

    /** События ещё без результата тарификации, в порядке добавления. */
    public function findUnrated(): array
    {
        $ids = $this->getEntityManager()->getConnection()->fetchFirstColumn(
            <<<'SQL'
                SELECT e.id
                FROM usage_events e
                LEFT JOIN event_ratings r ON r.event_id = e.id
                WHERE r.id IS NULL
                ORDER BY e.id
            SQL
        );

        if ($ids === []) {
            return [];
        }

        return $this->findBy(['id' => $ids]);
    }
}
