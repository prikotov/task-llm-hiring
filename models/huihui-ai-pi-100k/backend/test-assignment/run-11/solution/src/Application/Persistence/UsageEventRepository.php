<?php

declare(strict_types=1);

namespace App\Application\Persistence;

use App\Domain\Usage\RatedEventEntity;
use App\Domain\Usage\UsageEvent;
use App\Domain\Usage\UsageEventEntity;
use App\Domain\Usage\UsageEventRepository as UsageEventRepositoryInterface;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Реализация UsageEventRepository на базе Doctrine.
 *
 * findUnrated() — все импортированные события, которые ещё не рассчитаны
 * (нет соответствующей записи в rated_events по (provider, provider_request_id)).
 */
final class UsageEventRepository implements UsageEventRepositoryInterface
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * @return UsageEvent[]
     */
    public function findUnrated(): array
    {
        $dql = sprintf(
            'SELECT e FROM %s e
             WHERE NOT EXISTS (
                 SELECT 1 FROM %s r
                 WHERE r.provider = e.provider AND r.providerRequestId = e.providerRequestId
             )
             ORDER BY e.ts ASC',
            UsageEventEntity::class,
            RatedEventEntity::class,
        );

        /** @var UsageEventEntity[] $entities */
        $entities = $this->em->createQuery($dql)->getResult();

        return array_values(array_map(static fn (UsageEventEntity $e) => $e->toDomain(), $entities));
    }
}
