<?php

declare(strict_types=1);

namespace App\Shared\Persistence;

use Doctrine\DBAL\Connection;
use LogicException;
use RuntimeException;

/**
 * Data-access layer. All SQL lives in this class; command and domain
 * code depend only on it.
 */
final class Repository
{
    public const STATE_RATED   = 'rated';
    public const STATE_UNRATED = 'unrated';

    public function __construct(private readonly Connection $connection)
    {
    }

    public function beginTransaction(): void
    {
        if ($this->connection->isTransactionActive()) {
            throw new LogicException('Transaction already active.');
        }
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollBack(): void
    {
        $this->connection->rollBack();
    }

    public function inTransaction(): bool
    {
        return $this->connection->isTransactionActive();
    }

    public function nowIso(): string
    {
        return date('c');
    }

    /**
     * @param array<int, mixed> $params
     * @return list<array<string, mixed>>
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return array_values($this->connection->fetchAllAssociative($sql, array_values($params)));
    }

    /**
     * @param array<int, mixed> $params
     * @return array<string, mixed>|null
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $row = $this->connection->fetchAssociative($sql, array_values($params));

        return $row === false ? null : $row;
    }

    /**
     * @param array<int, mixed> $params
     */
    public function execute(string $sql, array $params = []): int
    {
        return $this->connection->executeStatement($sql, array_values($params));
    }

    // -- providers ---------------------------------------------------------

    public function upsertProvider(
        string $code,
        string $name,
        ?int $windowStartMin,
        ?int $windowEndMin,
        ?string $multiplier
    ): void {
        $this->execute(
            'INSERT INTO billing_providers
                (code, name, off_peak_start_min, off_peak_end_min, off_peak_multiplier)
             VALUES (?, ?, ?, ?, ?)
             ON CONFLICT (code) DO UPDATE SET
                name = EXCLUDED.name,
                off_peak_start_min = EXCLUDED.off_peak_start_min,
                off_peak_end_min   = EXCLUDED.off_peak_end_min,
                off_peak_multiplier = EXCLUDED.off_peak_multiplier',
            array_values([$code, $name, $windowStartMin, $windowEndMin, $multiplier])
        );
    }

    /**
     * @return array{startMin: int, endMin: int, multiplier: string}|null
     */
    public function fetchOffPeak(string $providerCode): ?array
    {
        $row = $this->fetchOne(
            'SELECT off_peak_start_min AS start_min,
                    off_peak_end_min   AS end_min,
                    off_peak_multiplier AS multiplier
             FROM billing_providers WHERE code = ?',
            array_values([$providerCode])
        );
        if ($row === null
            || $row['start_min'] === null
            || $row['end_min'] === null
            || $row['multiplier'] === null
        ) {
            return null;
        }

        return [
            'startMin'   => (int) $row['start_min'],
            'endMin'     => (int) $row['end_min'],
            'multiplier' => (string) $row['multiplier'],
        ];
    }

    // -- models ------------------------------------------------------------

    public function upsertModel(string $provider, string $code): void
    {
        $this->execute(
            'INSERT INTO billing_models (provider, code)
             VALUES (?, ?)
             ON CONFLICT (provider, code) DO NOTHING',
            array_values([$provider, $code])
        );
    }

    // -- price rows ---------------------------------------------------------

    /** @return int the row id */
    public function upsertPriceRow(
        string $provider,
        string $model,
        string $validFromIso,
        string $currency,
        string $unit,
        string $ratesJson
    ): int {
        $this->execute(
            'INSERT INTO billing_price_rows
                (provider, model, valid_from, currency, unit, rates_json)
             VALUES (?, ?, ?, ?, ?, ?)
             ON CONFLICT (provider, model, valid_from) DO UPDATE SET
                currency   = EXCLUDED.currency,
                unit       = EXCLUDED.unit,
                rates_json = EXCLUDED.rates_json',
            array_values([$provider, $model, $validFromIso, $currency, $unit, $ratesJson])
        );

        $row = $this->fetchOne(
            'SELECT id FROM billing_price_rows
             WHERE provider = ? AND model = ? AND valid_from = ?',
            array_values([$provider, $model, $validFromIso])
        );
        if ($row === null) {
            throw new RuntimeException('Price row vanished after upsert.');
        }

        return (int) $row['id'];
    }

    /**
     * The price row effective at the given moment: highest valid_from <= ts.
     *
     * @return array<string, mixed>|null one of id, provider, model,
     *         valid_from, currency, unit, rates_json
     */
    public function fetchEffectivePriceRow(string $provider, string $model, string $tsIso): ?array
    {
        return $this->fetchOne(
            'SELECT * FROM billing_price_rows
             WHERE provider = ? AND model = ? AND valid_from <= ?
             ORDER BY valid_from DESC, id DESC
             LIMIT 1',
            array_values([$provider, $model, $tsIso])
        );
    }

    // -- usage events --------------------------------------------------------

    /**
     * @param array<string, mixed> $e
     * @return bool true when inserted, false when the event already existed.
     */
    public function insertUsageEvent(array $e): bool
    {
        $rows = $this->execute(
            'INSERT INTO billing_usage_events
                (provider, provider_request_id, user_name, ts_utc, model_code,
                 tokens_input, tokens_output, tokens_cache_read, tokens_cache_write,
                 rating_state, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
             ON CONFLICT (provider, provider_request_id) DO NOTHING',
            array_values([
                (string) $e['provider'],
                (string) $e['providerRequestId'],
                (string) $e['user'],
                (string) $e['ts'],
                (string) $e['model'],
                (int) $e['tokens']['input'],
                (int) $e['tokens']['output'],
                (int) $e['tokens']['cache_read'],
                (int) $e['tokens']['cache_write'],
                self::STATE_UNRATED,
                (string) $e['now'],
                (string) $e['now'],
            ])
        );

        return $rows === 1;
    }

    /** @return list<array<string, mixed>> events whose rating must be attempted */
    public function fetchUnratedEvents(): array
    {
        return $this->fetchAll(
            'SELECT * FROM billing_usage_events WHERE rating_state = ? ORDER BY id ASC',
            array_values([self::STATE_UNRATED])
        );
    }

    /** @return list<array<string, mixed>> all events with rate_row_id not null */
    public function fetchRatedEvents(): array
    {
        return $this->fetchAll(
            'SELECT * FROM billing_usage_events
             WHERE rating_state = ? AND rate_row_id IS NOT NULL
             ORDER BY id ASC',
            array_values([self::STATE_RATED])
        );
    }

    /**
     * Events rated by one of the given price rows.
     *
     * @param list<int> $priceRowIds
     * @return list<array<string, mixed>>
     */
    public function fetchEventsRatByPriceRow(array $priceRowIds): array
    {
        $ids = array_map('intval', $priceRowIds);
        if ($ids === []) {
            return [];
        }
        $in = implode(',', array_fill(0, count($ids), '?'));
        $sql = 'SELECT * FROM billing_usage_events
             WHERE rating_state = ? AND rate_row_id IN ('.$in.')
             ORDER BY id ASC';

        return $this->fetchAll($sql, array_values([self::STATE_RATED, ...$ids]));
    }

    public function markUnrated(int $id, string $updatedAtIso): void
    {
        $this->execute(
            'UPDATE billing_usage_events SET
                rating_state = ?, exact_cost = NULL, rate_row_id = NULL,
                currency = NULL, off_peak_applied = FALSE, updated_at = ?
             WHERE id = ?',
            array_values([self::STATE_UNRATED, $updatedAtIso, $id])
        );
    }

    public function markRated(
        int $id,
        int $priceRowId,
        string $exact,
        string $currency,
        bool $offPeakApplied,
        string $updatedAtIso
    ): void {
        $this->execute(
            'UPDATE billing_usage_events SET
                rating_state = ?, rate_row_id = ?, exact_cost = ?,
                currency = ?, off_peak_applied = ?, updated_at = ?
             WHERE id = ?',
            array_values([
                self::STATE_RATED,
                $priceRowId,
                $exact,
                $currency,
                $offPeakApplied,
                $updatedAtIso,
                $id,
            ])
        );
    }

    /** @return list<array<string, mixed>> rated events for the period report */
    public function fetchRatedEventsInPeriod(?string $fromIso, ?string $toIso): array
    {
        $sql = 'SELECT * FROM billing_usage_events WHERE rating_state = ? AND rate_row_id IS NOT NULL';
        $params = [self::STATE_RATED];
        if ($fromIso !== null) {
            $sql .= ' AND ts_utc >= ?';
            $params[] = $fromIso;
        }
        if ($toIso !== null) {
            $sql .= ' AND ts_utc < ?';
            $params[] = $toIso;
        }
        $sql .= ' ORDER BY user_name ASC, model_code ASC, id ASC';

        return $this->fetchAll($sql, array_values($params));
    }

    // -- reprice journal -------------------------------------------------------

    public function insertRepriceRun(string $priceRowIdsJson, int $eventsCount, string $deltaUsd): int
    {
        $this->execute(
            'INSERT INTO billing_reprice_runs (price_row_ids_json, events_count, delta_usd, created_at)
             VALUES (?, ?, ?, ?)',
            array_values([$priceRowIdsJson, $eventsCount, $deltaUsd, date('c')])
        );

        return (int) $this->connection->lastInsertId();
    }
}
