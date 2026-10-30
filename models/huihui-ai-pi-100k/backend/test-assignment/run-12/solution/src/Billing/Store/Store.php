<?php

declare(strict_types=1);

namespace App\Billing\Store;

use App\Billing\Entity\Model;
use App\Billing\Entity\Provider;
use App\Billing\Entity\UsageEvent;
use App\Domain\Billing\OffPeakWindow;
use Doctrine\DBAL\ArrayParameterType;
use Doctrine\ORM\EntityManagerInterface;

final class Store
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    // ---------- providers ----------

    /**
     * upsert по code.
     */
    public function upsertProvider(Provider $p): void
    {
        $existing = $this->em->find(Provider::class, ['code' => $p->code]);
        if ($existing === null) {
            $this->em->persist($p);
        } else {
            $existing->name = $p->name;
            $existing->offPeakStartMinutes = $p->offPeakStartMinutes;
            $existing->offPeakEndMinutes = $p->offPeakEndMinutes;
            $existing->offPeakMultiplier = $p->offPeakMultiplier;
        }
        $this->em->flush();
    }

    public function provider(string $code): ?Provider
    {
        return $this->em->find(Provider::class, ['code' => $code]);
    }

    public function providerOffPeak(string $providerCode): ?OffPeakWindow
    {
        $p = $this->provider($providerCode);
        if ($p === null || $p->offPeakStartMinutes === null || $p->offPeakEndMinutes === null || $p->offPeakMultiplier === null) {
            return null;
        }

        try {
            return new OffPeakWindow($p->offPeakStartMinutes, $p->offPeakEndMinutes, $p->offPeakMultiplier);
        } catch (\InvalidArgumentException) {
            return null;
        }
    }

    // ---------- models ----------

    public function upsertModel(Model $m): void
    {
        $existing = $this->em->find(Model::class, ['id' => $m->id]);
        if ($existing === null) {
            $this->em->persist($m);
        }
        $this->em->flush();
    }

    public function model(string $id): ?Model
    {
        return $this->em->find(Model::class, ['id' => $id]);
    }

    // ---------- prices ----------

    public function upsertPriceRow(\App\Billing\Entity\PriceRow $row): void
    {
        $existing = $this->em->find(\App\Billing\Entity\PriceRow::class, ['id' => $row->id]);
        if ($existing === null) {
            $this->em->persist($row);
        } else {
            $existing->currency = $row->currency;
            $existing->unit = $row->unit;
            $existing->rates = $row->rates;
        }
        $this->em->flush();
    }

    public function priceRow(string $id): ?\App\Billing\Entity\PriceRow
    {
        return $this->em->find(\App\Billing\Entity\PriceRow::class, ['id' => $id]);
    }

    public function priceRowByParts(string $providerCode, string $modelCode, \DateTimeImmutable $validFrom): ?\App\Billing\Entity\PriceRow
    {
        $id = \App\Billing\Entity\EntityId::priceRow($providerCode, $modelCode, $validFrom);

        return $this->em->find(\App\Billing\Entity\PriceRow::class, ['id' => $id]);
    }

    /**
     * Строка цен, действовавшая в момент события (max valid_from <= ts).
     */
    public function pricedRowForEvent(string $providerCode, string $modelCode, \DateTimeImmutable $ts): ?\App\Billing\Entity\PriceRow
    {
        /** @var list<\App\Billing\Entity\PriceRow> */
        $rows = $this->em->createQueryBuilder()
            ->select('p')
            ->from(\App\Billing\Entity\PriceRow::class, 'p')
            ->where('p.providerCode = :p')
            ->andWhere('p.modelCode = :m')
            ->andWhere('p.validFrom <= :ts')
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->setParameter('p', $providerCode)
            ->setParameter('m', $modelCode)
            ->setParameter('ts', $ts)
            ->getQuery()
            ->getResult();

        return $rows[0] ?? null;
    }

    // ---------- usage events ----------

    /**
     * Вставка события, если его нет (дедупликация по (provider, request_id)).
     * Возвращает true, если событие было вставлено, false — если был дубль.
     */
    public function insertUsageEventIfAbsent(UsageEvent $e): bool
    {
        $existing = $this->em->find(UsageEvent::class, ['id' => $e->id]);
        if ($existing !== null) {
            return false;
        }

        $this->em->persist($e);
        $this->em->flush();

        return true;
    }

    public function usageEvent(string $id): ?UsageEvent
    {
        return $this->em->find(UsageEvent::class, ['id' => $id]);
    }

    /**
     * События, ещё не тарифицированные (isRated = false).
     *
     * @return list<UsageEvent>
     */
    public function allUnratedEvents(): array
    {
        /** @var list<UsageEvent> */
        return $this->em->createQueryBuilder()
            ->select('u')
            ->from(UsageEvent::class, 'u')
            ->where('u.isRated = false')
            ->orderBy('u.ts', 'ASC')
            ->getQuery()
            ->getResult();
    }

    public function markUsageEventRated(string $id): void
    {
        $e = $this->em->find(UsageEvent::class, ['id' => $id]);
        if ($e === null) {
            return;
        }
        $e->isRated = true;
        $this->em->flush();
    }

    public function usageEventsByProviderAndRequest(string $providerCode, string $requestId): ?UsageEvent
    {
        return $this->em->find(UsageEvent::class, ['id' => \App\Billing\Entity\EntityId::event($providerCode, $requestId)]);
    }

    // ---------- rated events ----------

    /**
     * Вставка или обновление rated события (reprice).
     */
    public function upsertRatedEvent(\App\Billing\Entity\RatedEvent $e): void
    {
        $existing = $this->em->find(\App\Billing\Entity\RatedEvent::class, ['eventId' => $e->eventId]);
        if ($existing === null) {
            $this->em->persist($e);
        } else {
            $existing->priceRowId = $e->priceRowId;
            $existing->eventTs = $e->eventTs;
            $existing->user = $e->user;
            $existing->tokens = $e->tokens;
            $existing->costUsd = $e->costUsd;
            $existing->createdAt = $e->createdAt;
            $existing->repricedAt = $e->repricedAt;
        }
        $this->em->flush();
    }

    public function ratedEvent(string $eventId): ?\App\Billing\Entity\RatedEvent
    {
        return $this->em->find(\App\Billing\Entity\RatedEvent::class, ['eventId' => $eventId]);
    }

    /**
     * @return list<\App\Billing\Entity\RatedEvent>
     */
    public function ratedEvents(?\DateTimeImmutable $from, ?\DateTimeImmutable $to): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('r')
            ->from(\App\Billing\Entity\RatedEvent::class, 'r');

        if ($from !== null) {
            $qb->where('r.eventTs >= :from')->setParameter('from', $from);
        }
        if ($to !== null) {
            $qb->andWhere('r.eventTs < :to')->setParameter('to', $to);
        }

        /** @var list<\App\Billing\Entity\RatedEvent> */
        return $qb->getQuery()->getResult();
    }

    /**
     * All rated events, priced by one of given price row ids.
     *
     * @param list<string> $rowIds
     * @return list<\App\Billing\Entity\RatedEvent>
     */
    public function ratedEventsPricedByRows(array $rowIds): array
    {
        if ($rowIds === []) {
            return [];
        }

        /** @var list<\App\Billing\Entity\RatedEvent> */
        return $this->em->createQueryBuilder()
            ->select('r')
            ->from(\App\Billing\Entity\RatedEvent::class, 'r')
            ->where('r.priceRowId IN (:ids)')
            ->setParameter('ids', $rowIds, ArrayParameterType::STRING)
            ->getQuery()
            ->getResult();
    }

    // ---------- reprice logs ----------

    public function persistRepriceLog(\App\Billing\Entity\RepriceLog $log): void
    {
        $this->em->persist($log);
        $this->em->flush();
    }

    // ---------- misc ----------

    public function flush(): void
    {
        $this->em->flush();
    }

    public function clear(): void
    {
        $this->em->clear();
    }

    /**
     * Возвращает "<providerCode>/<modelCode>" из usage-события по id, или null.
     */
    public function modelRefForEvent(string $eventId): ?string
    {
        $e = $this->em->find(UsageEvent::class, $eventId);
        if ($e === null) {
            return null;
        }

        return $e->providerCode . '/' . $e->modelCode;
    }

}
