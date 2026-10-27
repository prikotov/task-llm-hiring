<?php

declare(strict_types=1);

namespace App\Repository;

use App\Domain\Model;
use App\Domain\PriceRow;
use App\Domain\Provider;
use App\Domain\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Точка доступа к БД для всех сценариев приложения.
 */
final class DomainRepository
{
    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {}

    // ---- providers ----

    public function findProvider(string $code): ?Provider
    {
        return $this->em->getRepository(Provider::class)->findOneBy(['code' => $code]);
    }

    public function saveProvider(Provider $provider): void
    {
        $this->em->persist($provider);
    }

    // ---- models ----

    public function findModel(string $providerCode, string $code): ?Model
    {
        return $this->em->getRepository(Model::class)
            ->findOneBy(['provider' => $this->findProvider($providerCode), 'code' => $code]);
    }

    public function saveModel(Model $model): void
    {
        $this->em->persist($model);
    }

    // ---- price rows ----

    public function findPriceRow(string $providerCode, string $modelCode, \DateTimeImmutable $validFrom): ?PriceRow
    {
        $model = $this->findModel($providerCode, $modelCode);
        if ($model === null) {
            return null;
        }

        return $this->em->getRepository(PriceRow::class)
            ->findOneBy(['model' => $model, 'validFrom' => $validFrom]);
    }

    /**
     * Строка цены с наибольшим valid_from <= ts для данного (provider, model), или null.
     */
    public function findRowEffectiveAt(string $providerCode, string $modelCode, \DateTimeImmutable $ts): ?PriceRow
    {
        $model = $this->findModel($providerCode, $modelCode);
        if ($model === null) {
            return null;
        }

        $qb = $this->em->getRepository(PriceRow::class)->createQueryBuilder('row');
        $qb->andWhere('row.model = :model')->setParameter('model', $model);
        $qb->andWhere('row.validFrom <= :ts');
        $qb->setParameter('ts', $ts);
        $qb->orderBy('row.validFrom', 'DESC');
        $qb->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult();
    }

    public function savePriceRow(PriceRow $row): void
    {
        $this->em->persist($row);
    }

    /**
     * Upsert: если строка (provider, model, valid_from) уже существует — обновляет,
     * иначе сохраняет.
     */
    public function upsertPriceRow(PriceRow $row): void
    {
        $providerCode = $row->getModel()->getProvider()->getCode();
        $modelCode = $row->getModel()->getCode();
        $validFrom = $row->getValidFrom();

        $existing = $this->findPriceRow($providerCode, $modelCode, $validFrom);

        if ($existing !== null) {
            $existing->setUnit($row->getUnit());
            $existing->setCurrency($row->getCurrency());
            $existing->setRates($row->getRates());
            $this->em->persist($existing);
            return;
        }

        $this->em->persist($row);
    }

    // ---- usage events ----

    public function findUsageEvent(string $providerCode, string $requestId): ?UsageEvent
    {
        return $this->em->getRepository(UsageEvent::class)->findOneBy([
            'providerCode' => $providerCode,
            'providerRequestId' => $requestId,
        ]);
    }

    public function saveUsageEvent(UsageEvent $event): void
    {
        $this->em->persist($event);
        $this->em->flush();
    }

    public function findPendingOrUnrated(): array
    {
        return $this->em->getRepository(UsageEvent::class)
            ->createQueryBuilder('e')
            ->where('e.status IN (:s1, :s2)')
            ->setParameter('s1', UsageEvent::STATUS_PENDING)
            ->setParameter('s2', UsageEvent::STATUS_UNRATED)
            ->getQuery()
            ->getResult();
    }

    public function findRatedInRange(\DateTimeImmutable $from, ?\DateTimeImmutable $to = null): array
    {
        $qb = $this->em->getRepository(UsageEvent::class)->createQueryBuilder('e');
        $qb->andWhere('e.status = :status')->setParameter('status', UsageEvent::STATUS_RATED);
        $qb->andWhere('e.ts >= :from')->setParameter('from', $from);

        if ($to !== null) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Все events, рассчитанные конкретной строкой (для reprice).
     */
    public function findEventsByPriceRow(PriceRow $row): array
    {
        return $this->em->getRepository(UsageEvent::class)->findBy([
            'priceRow' => $row,
            'status' => UsageEvent::STATUS_RATED,
        ]);
    }
}
