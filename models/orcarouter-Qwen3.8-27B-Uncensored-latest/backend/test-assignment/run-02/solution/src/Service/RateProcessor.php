<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Тарификация ещё не рассчитанных событий (включая повторные попытки unrated).
 *
 * Событие считается, если для его (provider, model) найдена строка цен с
 * наибольшим valid_from <= ts. Иначе событие остаётся unrated — это не ошибка:
 * цены могли подтянуться позже и при следующем запуске будет повторена попытка.
 *
 * @see BillingEngine — фактическая арифметика и правила.
 */
final class RateProcessor
{
    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly BillingEngine $engine,
    ) {
    }

    /**
     * @return array{rated: int, unrated: array<int, array{provider: string, provider_request_id: string, ts: string}>}
     */
    public function ratePending(): array
    {
        $conn = $this->em->getConnection();
        $pending = $conn->fetchAllAssociative(<<<'SQL'
            SELECT id, provider, provider_request_id, model_code, ts,
                   tokens_input, tokens_output, tokens_cache_read, tokens_cache_write
            FROM usage_events
            WHERE status = 'pending'
            ORDER BY id
        SQL);

        $rated = 0;
        $unrated = [];
        $now = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));

        $this->em->wrapInTransaction(function () use ($pending, &$rated, &$unrated, $now): void {
            $conn = $this->em->getConnection();
            foreach ($pending as $event) {
                $tsUtc = new \DateTimeImmutable((string) $event['ts'], new \DateTimeZone('UTC'));
                $price = $this->selectApplicablePrice($conn, (string) $event['provider'], (string) $event['model_code'], $tsUtc);

                if ($price === null) {
                    $unrated[] = [
                        'provider' => (string) $event['provider'],
                        'provider_request_id' => (string) $event['provider_request_id'],
                        'ts' => $tsUtc->format('Y-m-d\\TH:i:s\\Z'),
                    ];
                    continue;
                }

                $rates = json_decode((string) $price['rates'], true);
                $rates = is_array($rates) ? $rates : [];
                $cost = $this->engine->cost(
                    (string) $price['unit'],
                    $rates,
                    (int) $event['tokens_input'],
                    (int) $event['tokens_output'],
                    (int) $event['tokens_cache_read'],
                    (int) $event['tokens_cache_write'],
                    isset($price['off_peak_window_start']) ? (string) $price['off_peak_window_start'] : null,
                    isset($price['off_peak_window_end']) ? (string) $price['off_peak_window_end'] : null,
                    isset($price['off_peak_multiplier']) ? (string) $price['off_peak_multiplier'] : null,
                    $tsUtc,
                );

                $conn->executeStatement(
                    "UPDATE usage_events
                        SET status = 'rated', price_row_id = :pid, cost_usd = :cost, rated_at = :now
                      WHERE id = :id",
                    ['pid' => $price['id'], 'cost' => $cost, 'now' => \App\Support\SqlDateTime::utc($now), 'id' => $event['id']],
                );
                $rated++;
            }
        });

        return ['rated' => $rated, 'unrated' => $unrated];
    }

    private function selectApplicablePrice(Connection $conn, string $provider, string $model, \DateTimeImmutable $ts): ?array
    {
        $row = $conn->fetchAssociative(
            <<<'SQL'
                SELECT pr.id, pr.unit, pr.rates,
                       p.off_peak_window_start, p.off_peak_window_end, p.off_peak_multiplier
                FROM price_rows pr
                JOIN catalog_models m ON m.id = pr.model_id
                JOIN providers p ON p.code = m.provider
                WHERE m.provider = :provider
                  AND m.code = :model
                  AND pr.valid_from <= :ts
                ORDER BY pr.valid_from DESC
                LIMIT 1
            SQL,
            ['provider' => $provider, 'model' => $model, 'ts' => \App\Support\SqlDateTime::utc($ts)],
        );

        return $row === false ? null : $row;
    }
}
