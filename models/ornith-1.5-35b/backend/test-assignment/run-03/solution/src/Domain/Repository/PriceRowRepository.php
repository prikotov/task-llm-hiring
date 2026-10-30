<?php

declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Model;
use App\Domain\Entity\PriceRow;
use App\Domain\Entity\Provider;
use App\Domain\Entity\UsageEvent;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<PriceRow>
 */
final class PriceRowRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PriceRow::class);
    }

    public function save(PriceRow $row): void
    {
        $this->getEntityManager()->persist($row);
        $this->getEntityManager()->flush();
    }

    /**
     * Строка цены по id, либо null.
     */
    public function findForId(int $id): ?PriceRow
    {
        /** @var PriceRow|null $row */
        return $this->findOneBy(['id' => $id]);
    }

    /**
     * Актуальная для момента ts строка модели (наибольший valid_from ≤ ts).
     */
    public function findActiveFor(
        Model $model,
        \DateTimeImmutable $ts,
    ): ?PriceRow {
        /** @var PriceRow|null $row */
        return $this->createQueryBuilder('p')
            ->where('p.model = :model')
            ->andWhere('p.validFrom <= :ts')
            ->setParameter('model', $model)
            ->setParameter('ts', $ts)
            ->orderBy('p.validFrom', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /**
     * Строки с заданным valid_from, для upsert по (provider, model, valid_from).
     *
     * @return PriceRow[]
     */
    public function findByValidFrom(\DateTimeImmutable $validFrom): array
    {
        /** @var PriceRow[] $rows */
        return $this->findBy(['validFrom' => $validFrom]);
    }

    /**
     * Идемпотентный upsert строки цены по (provider, model, valid_from).
     *
     * Возвращает true, если строка либо добавлена, либо изменена;
     * false — если существующие данные совпадают с новыми (no-op).
     */
    public function upsert(
        Provider $provider,
        Model $model,
        \DateTimeImmutable $validFrom,
        string $currency,
        string $unit,
        ?string $rateInput,
        ?string $rateOutput,
        ?string $rateCacheRead,
        ?string $rateCacheWrite,
        ?string $rateAny,
    ): bool {
        /** @var PriceRow|null $existing */
        $existing = $this->findFirst($provider, $model, $validFrom);

        if (null === $existing) {
            /** @var PriceRow $row */
            $row = new PriceRow();
            $row->provider = $provider;
            $row->model = $model;
            $row->validFrom = $validFrom;
            $row->currency = $currency;
            $row->unit = $unit;
            $row->rateInput = $rateInput;
            $row->rateOutput = $rateOutput;
            $row->rateCacheRead = $rateCacheRead;
            $row->rateCacheWrite = $rateCacheWrite;
            $row->rateAny = $rateAny;
            $this->save($row);

            return true;
        }

        $changed = $existing->currency !== $currency
            || $existing->unit !== $unit
            || $existing->rateInput !== $rateInput
            || $existing->rateOutput !== $rateOutput
            || $existing->rateCacheRead !== $rateCacheRead
            || $existing->rateCacheWrite !== $rateCacheWrite
            || $existing->rateAny !== $rateAny;

        if ($changed) {
            $existing->currency = $currency;
            $existing->unit = $unit;
            $existing->rateInput = $rateInput;
            $existing->rateOutput = $rateOutput;
            $existing->rateCacheRead = $rateCacheRead;
            $existing->rateCacheWrite = $rateCacheWrite;
            $existing->rateAny = $rateAny;
            $this->save($existing);
        }

        return $changed;
    }

    /**
     * Строка для тройки (провайдер, модель, valid_from), либо null.
     *
     * @param Provider $provider
     * @param Model $model
     * @param \DateTimeImmutable $validFrom
     */
    public function findForTriple(
        Provider $provider,
        Model $model,
        \DateTimeImmutable $validFrom,
    ): ?PriceRow {
        return $this->findFirst($provider, $model, $validFrom);
    }

    /**
     * Строка для тройки (провайдер, модель, valid_from).
     *
     * @param Provider $provider
     * @param Model $model
     * @param \DateTimeImmutable $validFrom
     */
    private function findFirst(
        Provider $provider,
        Model $model,
        \DateTimeImmutable $validFrom,
    ): ?PriceRow {
        /** @var PriceRow|null $row */
        return $this->createQueryBuilder('p')
            ->where('p.provider = :provider')
            ->andWhere('p.model = :model')
            ->andWhere('p.validFrom = :validFrom')
            ->setParameter('provider', $provider)
            ->setParameter('model', $model)
            ->setParameter('validFrom', $validFrom)
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
