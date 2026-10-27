<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\IsoUtc;
use App\Domain\OffPeak;
use App\Domain\RateCalculator;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Types\Types;

/**
 * Тарификация ещё не рассчитанных событий (`status = 'pending'`).
 *
 * Для каждого события выбирается строка цен своей модели по правилу
 * «наибольшее valid_from ≤ ts» (valid_from включительно). Строка не найдена
 * -> событие остаётся pending (unrated), в следующий запуск будет повторена
 * попытка (цены могли подтянуться позже). Цена, найденная -> стоимость
 * события рассчитывается (б/оф-пика), округляется до 6 знаков HALF_UP,
 * событие становится rated.
 *
 * Идемпотентно: повторный запуск, если новых событий нет, даёт RATED=0.
 */
final class RateService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly RateCalculator $calculator,
    ) {
    }

    /**
     * @return array{
     *     rated: int,
     *     unrated: list<array{provider: string, provider_request_id: string, ts: \DateTimeImmutable}>
     * }
     */
    public function rate(): array
    {
        $rated = 0;
        $unrated = [];

        $this->connection->transactional(function () use (&$rated, &$unrated): void {
            $events = $this->connection->fetchAllAssociative(
                "SELECT provider_code,
                        provider_request_id,
                        model_code,
                        ts,
                        tokens_input,
                        tokens_output,
                        tokens_cache_read,
                        tokens_cache_write
                 FROM usage_events
                 WHERE status = 'pending'
                 ORDER BY provider_code, provider_request_id"
            );

            foreach ($events as $e) {
                $ts = new \DateTimeImmutable($e['ts'], new \DateTimeZone('UTC'));

                $price = $this->connection->fetchAssociative(
                    'SELECT unit, rates
                     FROM prices
                     WHERE provider_code = :provider
                       AND model_code = :model
                       AND valid_from <= :ts
                     ORDER BY valid_from DESC
                     LIMIT 1',
                    ['provider' => $e['provider_code'], 'model' => $e['model_code'], 'ts' => $ts],
                    ['ts' => Types::DATETIMETZ_IMMUTABLE]
                );

                if ($price === false) {
                    $unrated[] = [
                        'provider' => $e['provider_code'],
                        'provider_request_id' => $e['provider_request_id'],
                        'ts' => $ts,
                    ];

                    continue;
                }

                $price = [
                    'unit' => $price['unit'],
                    'rates' => json_decode($price['rates'], true),
                ];

                $offPeak = $this->offPeakFor((string) $e['provider_code']);

                $cost = $this->calculator->costUsd(
                    $price,
                    [
                        'input' => (int) $e['tokens_input'],
                        'output' => (int) $e['tokens_output'],
                        'cache_read' => (int) $e['tokens_cache_read'],
                        'cache_write' => (int) $e['tokens_cache_write'],
                    ],
                    $offPeak,
                    $ts
                );

                $this->connection->executeStatement(
                    "UPDATE usage_events
                        SET status = 'rated',
                            cost_usd = :cost_usd,
                            price_valid_from = (
                                SELECT valid_from
                                FROM prices
                                WHERE provider_code = :provider2
                                  AND model_code = :model2
                                  AND valid_from <= :ts2
                                ORDER BY valid_from DESC
                                LIMIT 1
                            )
                    WHERE provider_code = :provider
                      AND provider_request_id = :request",
                    [
                        'cost_usd' => $cost,
                        'provider' => $e['provider_code'],
                        'request' => $e['provider_request_id'],
                        'provider2' => $e['provider_code'],
                        'model2' => $e['model_code'],
                        'ts2' => $ts,
                    ],
                    ['ts2' => Types::DATETIMETZ_IMMUTABLE]
                );

                $rated += 1;
            }
        });

        return ['rated' => $rated, 'unrated' => $unrated];
    }

    private function offPeakFor(string $provider): ?OffPeak
    {
        $row = $this->connection->fetchAssociative(
            'SELECT off_peak_window_start, off_peak_window_end, off_peak_multiplier
             FROM providers
             WHERE code = :code',
            ['code' => $provider]
        );
        if ($row === false) {
            return null;
        }

        return OffPeak::fromProvider(
            $row['off_peak_window_start'] === null ? null : (string) $row['off_peak_window_start'],
            $row['off_peak_window_end'] === null ? null : (string) $row['off_peak_window_end'],
            $row['off_peak_multiplier'] === null ? null : (string) $row['off_peak_multiplier']
        );
    }
}
