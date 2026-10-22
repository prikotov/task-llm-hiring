<?php

declare(strict_types=1);

namespace App\Billing\Infrastructure\Persistence\Doctrine;

use App\Billing\Domain\Model\ModelReference;
use App\Billing\Domain\Pricing\PriceCatalog;
use App\Billing\Domain\Pricing\PriceRow;
use App\Billing\Infrastructure\Persistence\Doctrine\Entity\PriceRowRecord;
use Doctrine\ORM\EntityManagerInterface;

final class DoctrinePriceCatalog implements PriceCatalog
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly DoctrineCatalog $catalog,
    ) {
    }

    public function rowsForModel(ModelReference $model): array
    {
        $modelRecord = $this->catalog->findModelRecord($model);

        if (null === $modelRecord) {
            return [];
        }

        $records = $this->em->getRepository(PriceRowRecord::class)->findBy(
            ['model' => $modelRecord],
            ['validFrom' => 'ASC'],
        );

        return array_map(static fn (PriceRowRecord $r): PriceRow => $r->toDomain(), $records);
    }

    public function rowById(int $id): ?PriceRow
    {
        return $this->em->find(PriceRowRecord::class, $id)?->toDomain();
    }

    public function rowByNaturalKey(ModelReference $model, \DateTimeImmutable $validFrom): ?PriceRow
    {
        $modelRecord = $this->catalog->findModelRecord($model);

        if (null === $modelRecord) {
            return null;
        }

        return $this->em
            ->getRepository(PriceRowRecord::class)
            ->findOneBy(['model' => $modelRecord, 'validFrom' => $validFrom])
            ?->toDomain();
    }

    public function upsert(PriceRow $row): int
    {
        return $this->upsertInternal($row, flush: true);
    }

    /**
     * Upsert без немедленного flush — для пакетного импорта.
     * Требует явного flush() у вызывающего.
     */
    public function upsertDeferred(PriceRow $row): int
    {
        return $this->upsertInternal($row, flush: false);
    }

    public function flush(): void
    {
        $this->em->flush();
    }

    /**
     * @return list<PriceRow>
     */
    public function all(): array
    {
        $records = $this->em->getRepository(PriceRowRecord::class)->findBy([], ['validFrom' => 'ASC']);

        return array_map(static fn (PriceRowRecord $r): PriceRow => $r->toDomain(), $records);
    }

    private function upsertInternal(PriceRow $row, bool $flush): int
    {
        $modelRecord = $this->catalog->findModelRecord($row->model)
            ?? throw new \RuntimeException(sprintf('Модель "%s" отсутствует в справочнике', (string) $row->model));

        $existing = $this->em
            ->getRepository(PriceRowRecord::class)
            ->findOneBy(['model' => $modelRecord, 'validFrom' => $row->validFrom]);

        if (null !== $existing) {
            $existing->apply($row);
            $record = $existing;
        } else {
            $record = PriceRowRecord::fromDomain($row, $modelRecord);
            $this->em->persist($record);
        }

        if ($flush) {
            $this->em->flush();
        }

        return (int) $record->id();
    }
}
