<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\OffPeakWindow;
use App\Domain\TokenUsage;
use DateTimeImmutable;
use DateTimeZone;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception as DbalException;

/**
 * Тарификация ещё не рассчитанных событий (app:billing:rate).
 *
 * Выбор цены: строка цен модели с наибольшим valid_from <= ts (включительно).
 * Нет такой строки — событие остаётся unrated; следующий запуск повторит попытку.
 */
final class RatingService
{
    public function __construct(
        private readonly Connection $connection,
        private readonly RatesCalculator $calculator,
    ) {
    }

    /**
     * @throws DbalException
     *
     * @return array{rated: int, unrated: array<array{provider: string, id: string, ts: string}>}
     */
    public function ratePending(): array
    {
        $conn = $this->connection;
        $rated = 0;
        $unrated = [];

        $conn->transactional(function () use ($conn, &$rated, &$unrated) {
            $events = $conn->fetchAllAssociative(
                "SELECT e.id,
                        e.provider_code,
                        e.model_code,
                        e.ts,
                        e.tokens_input,
                        e.tokens_output,
                        e.tokens_cache_read,
                        e.tokens_cache_write,
                        p.off_peak
                 FROM usage_events e
                 LEFT JOIN providers p ON p.code = e.provider_code
                 WHERE e.status = 'unrated'
                 ORDER BY e.id",
            );

            foreach ($events as $e) {
                $line = $conn->fetchAssociative(
                    'SELECT id, unit, rates
                     FROM price_lines
                     WHERE provider_code = ? AND model_code = ? AND valid_from <= ?
                     ORDER BY valid_from DESC
                     LIMIT 1',
                    [$e['provider_code'], $e['model_code'], $e['ts']],
                );

                if ($line === false) {
                    $unrated[] = [
                        'provider' => $e['provider_code'],
                        'id' => (string) $e['id'],
                        'ts' => $e['ts'],
                    ];
                    continue;
                }

                $window = $e['off_peak'] !== null ? OffPeakWindow::decode((array) json_decode($e['off_peak'], true)) : null;
                $usage = new TokenUsage(
                    (int) $e['tokens_input'],
                    (int) $e['tokens_output'],
                    (int) $e['tokens_cache_read'],
                    (int) $e['tokens_cache_write'],
                );
                $rates = json_decode($line['rates'], true, 512, \JSON_THROW_ON_ERROR);

                $cost = $this->calculator->finalCost(
                    $usage,
                    $line['unit'],
                    $rates,
                    $window,
                    new DateTimeImmutable($e['ts'], new DateTimeZone('UTC')),
                );

                $conn->executeStatement(
                    "UPDATE usage_events
                     SET status = 'rated', cost = ?, price_line_id = ?, rated_at = NOW()
                     WHERE id = ?",
                    [$cost, (int) $line['id'], $e['id']],
                );
                ++$rated;
            }
        });

        return ['rated' => $rated, 'unrated' => $unrated];
    }
}
