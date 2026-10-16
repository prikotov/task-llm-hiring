<?php

declare(strict_types=1);

namespace App\Repository;

use App\Entity\UsageEvent;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;

final class UsageEventRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {
    }

    /** События, ожидающие тарификации (cost пуст). */
    public function findUnrated(): array
    {
        return $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.cost IS NULL')
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function existsByProviderAndRequestId(string $provider, string $providerRequestId): bool
    {
        $event = $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.provider = :provider')
            ->andWhere('e.providerRequestId = :providerRequestId')
            ->setMaxResults(1)
            ->getQuery()
            ->setParameter('provider', $provider)
            ->setParameter('providerRequestId', $providerRequestId)
            ->getOneOrNullResult();

        return $event !== null;
    }

    /**
     * Оценённые события, рассчитанные по заданным строкам цен (ретро-коррекция).
     *
     * @param list<int> $priceIds
     *
     * @return list<UsageEvent>
     */
    public function findRatedByPriceIds(array $priceIds): array
    {
        if ($priceIds === []) {
            return [];
        }

        return $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.cost IS NOT NULL')
            ->andWhere('e.price IN (:prices)')
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->setParameter('prices', $priceIds, ArrayParameterType::INTEGER)
            ->getResult();
    }
}
