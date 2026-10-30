<?php

declare(strict_types=1);

namespace App\Service\Billing;

use App\Domain\Billing\PricingCalculator;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий (включая повторный проход по
 * ранее unrated — цены могли подтянуться позже).
 *
 * Стоимость события: точная bcmath-арифметика (масштаб 24, без
 * промежуточных округлений), ровно одно округление итога до 6 знаков
 * HALF_UP. Unrated-событие — не ошибка: остаётся pending.
 */
final class BillingRater
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly PriceCandidateLoader $candidates,
        private readonly PricingCalculator $calculator,
    ) {}

    /**
     * @return array{rated: int, unrated: list<array{provider: string, request_id: string, ts: string}>}
     */
    public function ratePending(): array
    {
        return $this->em->wrapInTransaction(function (): array {
            $rows = $this->em->getConnection()->fetchAllAssociative(
                "SELECT id, provider, provider_request_id, ts,
                        tokens_input, tokens_output, tokens_cache_read, tokens_cache_write
                 FROM usage_events
                 WHERE status = 'pending'
                 ORDER BY id",
            );

            $ids = array_map(static fn (array $r): int => (int) $r['id'], $rows);
            $candidateMap = $this->candidates->loadForEvents($ids);

            $rated = 0;
            $unrated = [];

            foreach ($rows as $row) {
                $id = (int) $row['id'];
                $ts = new \DateTimeImmutable($row['ts'], new \DateTimeZone('UTC'));
                $candidate = $candidateMap[$id][0] ?? null;

                if ($candidate === null) {
                    $unrated[] = [
                        'provider' => (string) $row['provider'],
                        'request_id' => (string) $row['provider_request_id'],
                        'ts' => $ts->format('Y-m-d\TH:i:s\Z'),
                    ];
                    // остаётся pending — будет повторена в следующем проходе
                    continue;
                }

                $cost = $this->calculator->eventCost(
                    (int) $row['tokens_input'],
                    (int) $row['tokens_output'],
                    (int) $row['tokens_cache_read'],
                    (int) $row['tokens_cache_write'],
                    $candidate->unit,
                    $candidate->rates,
                    $candidate->offPeak,
                    $ts,
                );

                $updated = $this->em->getConnection()->executeStatement(
                    "UPDATE usage_events
                     SET status = 'rated', price_row_id = :priceRow, cost_usd = :cost
                     WHERE id = :id AND status = 'pending'",
                    ['priceRow' => $candidate->priceRowId, 'cost' => $cost, 'id' => $id],
                );

                if ($updated === 1) {
                    ++$rated;
                }
            }

            return ['rated' => $rated, 'unrated' => $unrated];
        });
    }
}
