<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\PriceRow;
use App\Entity\PriceRate;
use App\Entity\Provider;
use App\Entity\TokenModel;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;

/**
 * Данные домена биллинга: загрузка/поиск сущностей по естественным ключам
 * и списки, необходимые для тарификации/отчёта.
 */
final class BillingRepository
{
    public function __construct(private readonly EntityManagerInterface $em)
    {
    }

    public function findProviderByCode(string $code): ?Provider
    {
        return $this->em->createQueryBuilder()
            ->select('p')
            ->from(Provider::class, 'p')
            ->where('p.code = :code')
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findModelByProviderAndCode(Provider $provider, string $code): ?TokenModel
    {
        return $this->em->createQueryBuilder()
            ->select('m')
            ->from(TokenModel::class, 'm')
            ->where('m.provider = :provider')
            ->andWhere('m.code = :code')
            ->setParameter('provider', $provider)
            ->setParameter('code', $code)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findModelByProviderCodeAndCode(string $providerCode, string $modelCode): ?TokenModel
    {
        return $this->em->createQueryBuilder()
            ->select('m')
            ->from(TokenModel::class, 'm')
            ->join('m.provider', 'p')
            ->where('p.code = :providerCode')
            ->andWhere('m.code = :modelCode')
            ->setParameter('providerCode', $providerCode)
            ->setParameter('modelCode', $modelCode)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPriceRow(
        string $providerCode,
        string $modelCode,
        \DateTimeImmutable $validFrom,
    ): ?PriceRow {
        return $this->em->createQueryBuilder()
            ->select('r')
            ->from(PriceRow::class, 'r')
            ->join('r.provider', 'p')
            ->join('r.model', 'm')
            ->where('p.code = :providerCode')
            ->andWhere('m.code = :modelCode')
            ->andWhere('r.validFrom = :validFrom')
            ->setParameter('providerCode', $providerCode)
            ->setParameter('modelCode', $modelCode)
            ->setParameter('validFrom', $validFrom)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function findPriceRowById(int $id): ?PriceRow
    {
        return $this->em->getRepository(PriceRow::class)->find($id);
    }

    /**
     * Строки цен модели, отсортированные по growing `valid_from`.
     *
     * @return PriceRow[]
     */
    public function findPriceRowsForModel(Provider $provider, string $modelCode): array
    {
        return $this->em->createQueryBuilder()
            ->select('r')
            ->from(PriceRow::class, 'r')
            ->join('r.provider', 'p')
            ->join('r.model', 'm')
            ->where('p = :provider')
            ->andWhere('m.code = :modelCode')
            ->orderBy('r.validFrom', 'ASC')
            ->setParameter('provider', $provider)
            ->setParameter('modelCode', $modelCode)
            ->getQuery()
            ->getResult();
    }

    public function findEvent(string $providerCode, string $providerRequestId): ?UsageEvent
    {
        return $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->join('e.provider', 'p')
            ->where('p.code = :providerCode')
            ->andWhere('e.providerRequestId = :req')
            ->setParameter('providerCode', $providerCode)
            ->setParameter('req', $providerRequestId)
            ->getQuery()
            ->getOneOrNullResult();
    }

    /** @return UsageEvent[] */
    public function findUnratedEvents(): array
    {
        return $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.rated = false')
            ->getQuery()
            ->getResult();
    }

    /**
     * Рассчитанные события (опционально в периоде [from, to)).
     *
     * @return UsageEvent[]
     */
    public function findRatedEventsInPeriod(\DateTimeImmutable|null $from, \DateTimeImmutable|null $to): array
    {
        $qb = $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.rated = true');

        if (null !== $from) {
            $qb->andWhere('e.ts >= :from')->setParameter('from', $from);
        }

        if (null !== $to) {
            $qb->andWhere('e.ts < :to')->setParameter('to', $to);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Рассчитанные события, тарифицированные строками цен из списка id.
     *
     * @param list<int> $priceRowIds
     *
     * @return UsageEvent[]
     */
    public function findRatedEventsByPriceRowIds(array $priceRowIds): array
    {
        if ([] === $priceRowIds) {
            return [];
        }

        return $this->em->createQueryBuilder()
            ->select('e')
            ->from(UsageEvent::class, 'e')
            ->where('e.rated = true')
            ->andWhere('e.priceRow IN (:rows)')
            ->setParameter('rows', $priceRowIds)
            ->getQuery()
            ->getResult();
    }

    public function findRateByRowAndKind(PriceRow $priceRow, string $kind): ?PriceRate
    {
        return $this->em->createQueryBuilder()
            ->select('rt')
            ->from(PriceRate::class, 'rt')
            ->where('rt.priceRow = :row')
            ->andWhere('rt.kind = :kind')
            ->setParameter('row', $priceRow)
            ->setParameter('kind', $kind)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
