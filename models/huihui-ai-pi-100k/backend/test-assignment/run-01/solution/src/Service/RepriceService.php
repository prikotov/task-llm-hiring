<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Decimal;
use App\Domain\DomainException;
use App\Domain\Rater;
use App\Ingest\PriceFile;
use App\Entity\PriceRow;
use App\Entity\UsageEvent;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус): заменяет строки цен и пересчитывает
 * события, тарифицировавшиеся именно этими строками.
 *
 * Уже рассчитанные события не трогаем молча: их стоимость обновляется
 * атомарно в рамках одной смены цен, итоговый report без задвоений.
 */
final class RepriceService
{
    public function __construct(
        private readonly PriceImportService $import,
        private readonly Rater $rater,
    ) {
    }

    /**
     * @return RepriceResult
     * @throws DomainException
     */
    public function reprice(EntityManagerInterface $em, PriceFile $file): RepriceResult
    {
        if ($file->prices === []) {
            throw new DomainException('Файл не содержит строк цен ("prices" пуст)');
        }

        // 1. Применяем исправленные строки: upsert по (provider, model, valid_from),
        //    суммы заменяются. Сущности строк обновляются "на месте", поэтому
        //    ссылка событий priceRowId всё ещё указывает на корректную строку.
        $replacedRowIds = $this->import->import($em, $file);

        // 2. События, рассчитанные заменёнными строками.
        /** @var UsageEvent[] $events */
        $events = $em->createQuery(
            'SELECT e FROM '.UsageEvent::class.' e
             WHERE e.status = :status AND e.priceRow IN (:rowIds)
             ORDER BY e.id ASC'
        )
            ->setParameter('status', UsageEvent::STATUS_RATED)
            ->setParameter('rowIds', $replacedRowIds)
            ->getResult();

        $oldTotal = '0';
        $newTotal = '0';
        foreach ($events as $event) {
            assert($event->priceRow instanceof PriceRow);
            assert($event->cost !== null);
            $oldTotal = Decimal::addMoney($oldTotal, $event->cost);

            $event->cost = $this->rater->rate($event, $event->priceRow);
            $newTotal = Decimal::addMoney($newTotal, $event->cost);
        }

        $em->flush();

        $delta = Decimal::subMoney($newTotal, $oldTotal);
        if ($delta === '-0.000000') {
            $delta = '0.000000';
        }

        return new RepriceResult(\count($events), $delta);
    }
}

/**
 * @readonly
 */
final class RepriceResult
{
    public function __construct(
        public readonly int $repriced,
        public readonly string $delta,
    ) {
    }
}
