<?php

declare(strict_types=1);

namespace App\Service;

use App\Domain\Money;
use App\Domain\OffPeakWindow;
use Doctrine\DBAL\Connection;

final class RepriceEngine
{
    public function __construct(
        private readonly Connection $db,
        private readonly PricesImporter $pricesImporter
    ) {
    }

    /**
     * Apply repriced price lines and recalculate affected events.
     *
     * @return array{events: int, delta: string}
     */
    public function reprice(string $filePath): array
    {
        $json = file_get_contents($filePath);
        if ($json === false) {
            throw new \RuntimeException("Cannot read file: {$filePath}");
        }

        $data = json_decode($json, true);
        if ($data === null) {
            throw new \RuntimeException("Invalid JSON: {$filePath}");
        }

        $prices = $data['prices'] ?? [];
        if (empty($prices)) {
            return ['events' => 0, 'delta' => '0.000000'];
        }

        // Validate that providers and models exist
        foreach ($prices as $p) {
            $provider = $this->db->fetchAssociative('SELECT code FROM providers WHERE code = ?', [$p['provider']]);
            if (!$provider) {
                throw new \RuntimeException("Provider '{$p['provider']}' not found in database");
            }
            $model = $this->db->fetchAssociative('SELECT provider_code FROM models WHERE provider_code = ? AND code = ?', [$p['provider'], $p['model']]);
            if (!$model) {
                throw new \RuntimeException("Model '{$p['model']}' for provider '{$p['provider']}' not found in database");
            }
        }

        $this->db->beginTransaction();

        try {
            // 1. Find affected events (rated with the specific price lines being replaced)
            $affectedEvents = $this->findAffectedEvents($prices);

            // 2. Store old costs
            $oldCosts = [];
            foreach ($affectedEvents as $evt) {
                $oldCosts[(string) $evt['id']] = $evt['cost'] ?? '0';
            }

            // 3. Upsert the new price lines
            $this->pricesImporter->importPriceLines($prices);

            // 4. Recalculate costs for affected events using the NEW price line rates
            $delta = '0';
            $count = 0;

            // Build a map of price lines from the reprice file for direct lookup
            $newPriceMap = [];
            foreach ($prices as $p) {
                $key = $p['provider'] . "\x00" . $p['model'] . "\x00" . $this->normalizeTs($p['valid_from']);
                $newPriceMap[$key] = $p;
            }

            foreach ($affectedEvents as $evt) {
                // Find the new price line for this event
                $evtKey = $evt['provider_code'] . "\x00" . $evt['model_code'] . "\x00" . $this->normalizeTs($evt['price_valid_from']);
                $newPrice = $newPriceMap[$evtKey] ?? null;

                if ($newPrice === null) {
                    continue;
                }

                $tokens = [
                    'input' => (int) $evt['tokens_input'],
                    'output' => (int) $evt['tokens_output'],
                    'cache_read' => (int) $evt['tokens_cache_read'],
                    'cache_write' => (int) $evt['tokens_cache_write'],
                ];

                $newCost = $this->computeCostWithPriceLine($evt['provider_code'], $evt['ts'], $tokens, $newPrice['unit'], $newPrice['rates']);

                $oldCost = $oldCosts[(string) $evt['id']] ?? '0';
                $delta = Money::add($delta, Money::sub($newCost, $oldCost));

                $this->db->executeStatement(
                    'UPDATE usage_events SET cost = ? WHERE id = ?',
                    [$newCost, $evt['id']]
                );
                $count++;
            }

            $this->db->commit();

            return ['events' => $count, 'delta' => Money::format($delta, 6)];
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * @param array<array<string,mixed>> $prices
     */
    private function findAffectedEvents(array $prices): array
    {
        if (empty($prices)) {
            return [];
        }

        $conditions = [];
        $params = [];
        foreach ($prices as $p) {
            $conditions[] = '(provider_code = ? AND model_code = ? AND price_valid_from = ?::timestamptz)';
            $params[] = $p['provider'];
            $params[] = $p['model'];
            $params[] = $p['valid_from'];
        }

        $where = implode(' OR ', $conditions);

        return $this->db->fetchAllAssociative(
            "SELECT id, provider_code, model_code, ts,
                    tokens_input, tokens_output, tokens_cache_read, tokens_cache_write, cost,
                    price_valid_from
             FROM usage_events
             WHERE status = 'rated' AND ({$where})
             ORDER BY id",
            $params
        );
    }

    /**
     * Compute cost using a specific price line (unit + rates) and off-peak rules.
     */
    private function computeCostWithPriceLine(
        string $providerCode,
        string $ts,
        array $tokens,
        string $unit,
        array $rates
    ): string {
        $baseCost = $this->computeBaseCost($unit, $rates, $tokens);

        // Apply off-peak if applicable
        $provider = $this->db->fetchAssociative(
            'SELECT off_peak_window_start, off_peak_window_end, off_peak_multiplier FROM providers WHERE code = ?',
            [$providerCode]
        );

        $cost = $baseCost;
        if ($provider && $provider['off_peak_window_start'] !== null) {
            $offPeak = new OffPeakWindow(
                $provider['off_peak_window_start'],
                $provider['off_peak_window_end'],
                $provider['off_peak_multiplier']
            );
            $timeOfDay = $this->extractTimeOfDay($ts);
            if ($offPeak->contains($timeOfDay)) {
                $cost = Money::mul($cost, $offPeak->getMultiplier());
            }
        }

        return Money::roundHalfUp($cost, 6);
    }

    private function computeBaseCost(string $unit, array $rates, array $tokens): string
    {
        $divisor = '1000000';

        if ($unit === 'per_1m_tokens') {
            $cost = '0';
            $cost = Money::add($cost, Money::div(Money::mul((string) $tokens['input'], $rates['input'] ?? '0'), $divisor));
            $cost = Money::add($cost, Money::div(Money::mul((string) $tokens['output'], $rates['output'] ?? '0'), $divisor));
            $cost = Money::add($cost, Money::div(Money::mul((string) $tokens['cache_read'], $rates['cache_read'] ?? '0'), $divisor));
            $cost = Money::add($cost, Money::div(Money::mul((string) $tokens['cache_write'], $rates['cache_write'] ?? '0'), $divisor));
            return $cost;
        }

        if ($unit === 'per_1m_tokens_unified') {
            $totalTokens = $tokens['input'] + $tokens['output'] + $tokens['cache_read'] + $tokens['cache_write'];
            return Money::div(Money::mul((string) $totalTokens, $rates['any'] ?? '0'), $divisor);
        }

        throw new \InvalidArgumentException("Unknown unit: {$unit}");
    }

    private function extractTimeOfDay(string $ts): int
    {
        $dt = new \DateTimeImmutable($ts, new \DateTimeZone('UTC'));
        return (int) $dt->format('G') * 60 + (int) $dt->format('i');
    }

    private function normalizeTs(string $ts): string
    {
        $dt = new \DateTimeImmutable($ts, new \DateTimeZone('UTC'));
        return $dt->format('Y-m-d\TH:i:s\Z');
    }
}
