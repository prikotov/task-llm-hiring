<?php

declare(strict_types=1);

namespace App\Billing\Application;

use App\Billing\Domain\CostAdjustment;
use App\Pricing\Application\PriceImporter;
use App\Pricing\Application\PriceList;
use App\Pricing\Domain\PriceRow;
use App\Pricing\Infrastructure\PricingRepository;
use App\Shared\Decimal;
use App\Usage\Domain\UsageEvent;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цен: upsert исправленных строк и пересчёт затронутых уже рассчитанных событий.
 * Стоимость события перезаписывается, старое и новое значение фиксируются в журнале cost_adjustment.
 */
final class RepricingService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceImporter $importer,
        private readonly PricingRepository $pricing,
    ) {
    }

    /** @return array{events: int, delta: Decimal} */
    public function reprice(PriceList $corrections): array
    {
        return $this->em->wrapInTransaction(function () use ($corrections): array {
            $changed = $this->importer->upsertPrices($corrections, [], allowChangeOfUsedRows: true);
            $now = new DateTimeImmutable();
            $delta = Decimal::zero();
            $count = 0;
            $seen = [];

            foreach ($changed as ['row' => $row]) {
                foreach ($this->candidates($row) as $event) {
                    $oid = spl_object_id($event);
                    if (isset($seen[$oid])) {
                        continue;
                    }
                    $seen[$oid] = true;
                    $oldRow = $event->priceRow();
                    $oldCost = $event->cost() ?? Decimal::zero();
                    $newRow = $this->pricing->effectiveRow($event->providerCode(), $event->modelCode(), $event->ts());
                    if ($newRow === null || $oldRow === null) {
                        continue;
                    }
                    // пересчитываем только события, тарифицированные заменёнными строками
                    // (изменённая строка или новая строка, вытеснившая прежнюю для момента события)
                    if ($oldRow !== $row && $newRow !== $row) {
                        continue;
                    }
                    $newCost = $event->rateWith($newRow, $now);
                    $this->em->persist(new CostAdjustment($event, $newRow, $oldCost, $newCost, $now));
                    $delta = $delta->add($newCost->sub($oldCost));
                    ++$count;
                }
            }
            $this->em->flush();

            return ['events' => $count, 'delta' => $delta];
        });
    }

    /** @return list<UsageEvent> рассчитанные события той же модели с ts >= valid_from строки */
    private function candidates(PriceRow $row): array
    {
        $model = $row->model();

        /** @var list<UsageEvent> */
        return $this->em->createQueryBuilder()
            ->select('e')->from(UsageEvent::class, 'e')
            ->where('e.ratedAt IS NOT NULL')
            ->andWhere('e.providerCode = :p')->andWhere('e.modelCode = :m')
            ->andWhere('e.ts >= :from')
            ->setParameter('p', $model->provider()->code())
            ->setParameter('m', $model->code())
            ->setParameter('from', $row->validFrom(), 'datetimetz_immutable')
            ->orderBy('e.id')
            ->getQuery()->getResult();
    }
}
