<?php

declare(strict_types=1);

namespace App\Persistence\Repository;

use App\Domain\Common\Iso8601Utc as Utc;
use App\Persistence\Entity\UsageEvent;
use App\Persistence\Enum\EventStatus;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;

final class UsageEventRepository
{
    private const ARRAY_HYDRATION = Query::HYDRATE_ARRAY;

    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    /**
     * События, ожидающие расчёта (pending и ранее unrated — повторные попытки),
     * порциями по возрастанию id.
     *
     * @return list<UsageEvent>
     */
    public function findNotRated(int $limit, int $afterId = 0): array
    {
        return $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.status IN (:statuses)')
            ->andWhere('e.id > :afterId')
            ->setParameter('statuses', [EventStatus::Pending, EventStatus::Unrated])
            ->setParameter('afterId', $afterId)
            ->orderBy('e.id', 'ASC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Рассчитанные события в отчётном периоде: [from, to) по ts события.
     *
     * @return list<UsageEvent>
     */
    public function findRatedInRange(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.status = :status')
            ->setParameter('status', EventStatus::Rated);

        if ($from !== null) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', Utc::toMutableUtc($from));
        }
        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', Utc::toMutableUtc($to));
        }

        return $qb->getQuery()->getResult();
    }

    /** Рассчитанные события, тарифицированные заданными строками цен. */
    /** @return list<UsageEvent> */
    public function findRatedByPriceRowIds(array $priceRowIds): array
    {
        if ($priceRowIds === []) {
            return [];
        }

        return $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.status = :status')
            ->andWhere('e.priceRowId IN (:rowIds)')
            ->setParameter('status', EventStatus::Rated)
            ->setParameter('rowIds', array_values($priceRowIds))
            ->orderBy('e.id', 'ASC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Существующие пары (provider, provider_request_id) в заданном множестве.
     *
     * @param list<string> $providers
     * @param list<string> $requestIds
     * @return list<array{provider: string, providerRequestId: string}>
     */
    public function findExistingKeys(array $providers, array $requestIds): array
    {
        if ($providers === [] || $requestIds === []) {
            return [];
        }

        return $this->em->createQueryBuilder()
            ->select('e.provider AS provider', 'e.providerRequestId AS providerRequestId')
            ->from(UsageEvent::class, 'e')
            ->where('e.provider IN (:providers)')
            ->andWhere('e.providerRequestId IN (:requestIds)')
            ->setParameter('providers', array_values($providers))
            ->setParameter('requestIds', array_values($requestIds))
            ->getQuery()
            ->getResult(self::ARRAY_HYDRATION);
    }
}
