<?php

declare(strict_types=1);

namespace App\Service\Billing;

use App\Domain\Billing\PricingCalculator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Ретро-коррекция цены (бонус): после upsert исправленных строк цен
 * пересчитываются события, тарифицированные заменёнными строками,
 * по актуальному справочнику.
 */
final class RepriceService
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceCandidateLoader $candidates,
        private readonly PricingCalculator $calculator,
    ) {}

    /**
     * @param list<int> $replacedRowIds строки цен, значения которых были заменены
     *
     * @return array{count: int, deltaUsd: string} delta = новая сумма − старая сумма
     */
    public function repricingByRows(array $replacedRowIds): array
    {
        if ($replacedRowIds === []) {
            return ['count' => 0, 'deltaUsd' => '0.000000'];
        }

        return $this->em->wrapInTransaction(function () use ($replacedRowIds): array {
            $placeholders = implode(',', array_map('\strval', $replacedRowIds));
            $events = $this->em->getConnection()->fetchAllAssociative(
                "SELECT id, provider, model, ts,
                        tokens_input, tokens_output, tokens_cache_read, tokens_cache_write,
                        cost_usd AS old_cost
                 FROM usage_events
                 WHERE status = 'rated' AND price_row_id IN ($placeholders)
                 ORDER BY id",
            );

            $ids = array_map(static fn (array $e): int => (int) $e['id'], $events);
            $candidateMap = $this->candidates->loadForEvents($ids);

            $count = 0;
            $delta = '0';

            foreach ($events as $event) {
                $id = (int) $event['id'];
                $candidate = $candidateMap[$id][0] ?? null;
                if ($candidate === null) {
                    continue; // справочник не позволяет пересчитать — оставляем
                }

                $ts = new \DateTimeImmutable($event['ts'], new \DateTimeZone('UTC'));
                $newCost = $this->calculator->eventCost(
                    (int) $event['tokens_input'],
                    (int) $event['tokens_output'],
                    (int) $event['tokens_cache_read'],
                    (int) $event['tokens_cache_write'],
                    $candidate->unit,
                    $candidate->rates,
                    $candidate->offPeak,
                    $ts,
                );

                $this->em->getConnection()->executeStatement(
                    'UPDATE usage_events SET cost_usd = :cost, price_row_id = :row WHERE id = :id',
                    ['cost' => $newCost, 'row' => $candidate->priceRowId, 'id' => $id],
                );

                $oldCost = PricingCalculator::format6((string) $event['old_cost']);
                $delta = bcadd($delta, bcsub($newCost, $oldCost, 6), 6);
                ++$count;
            }

            return ['count' => $count, 'deltaUsd' => PricingCalculator::format6($delta)];
        });
    }
}
