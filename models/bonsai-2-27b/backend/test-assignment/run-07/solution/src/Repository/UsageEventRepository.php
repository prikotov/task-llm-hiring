<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UsageEvent;
use Doctrine\Persistence\ManagerRegistry;

/**
 * Хранилище событий использования.
 */
final class UsageEventRepository
{
    public function __construct(private readonly ManagerRegistry $registry)
    {
    }

    public function getDoctrine(): \Doctrine\ORM\EntityManager
    {
        return $this->registry->getManager();
    }

    public function findNewEvents(int $limit): array
    {
        return $this->getDoctrine()->createQueryBuilder()
            ->from(UsageEvent::class)
            ->where('u.status', '=', UsageEvent::STATUS_NEW)
            ->orderBy('u.ts')
            ->orderBy('u.provider')
            ->orderBy('u.providerRequestId')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function getProviderByCode(string $code): ?App\Entity\Provider
    {
        return $this->getDoctrine()->getRepository(App\Entity\Provider::class)
            ->findOne(['code' => $code]);
    }

    /**
     * Строка истории цен, валидная на момент $ts.
     * Возвращает последнюю строку с validFrom <= $ts.
     */
    public function findPriceRowFor(
        string $provider,
        string $model,
        \DateTimeImmutable $ts,
    ): ?App\Entity\PriceRow {
        return $this->getDoctrine()->createQueryBuilder()
            ->from(App\Entity\PriceRow::class)
            ->where('p.provider', '=', $provider)
            ->andWhere('p.model', '=', $model)
            ->andWhere('p.validFrom', '<=', $ts)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->singleResultOrNull();
    }

    public function save(UsageEvent $event): void
    {
        $this->getDoctrine()->flush();
    }

    public function countRouted(): int
    {
        return (int) $this->getDoctrine()->createQueryBuilder()
            ->from(UsageEvent::class)
            ->where('u.status', '=', UsageEvent::STATUS_RATED)
            ->getQuery()
            ->singleResult();
    }
}
