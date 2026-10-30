<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;

final class PricesImporter
{
    public function __construct(private readonly Connection $db)
    {
    }

    /**
     * Import providers, models, and prices from a JSON file.
     * Idempotent upsert.
     */
    public function import(string $filePath): void
    {
        $json = file_get_contents($filePath);
        if ($json === false) {
            throw new \RuntimeException("Cannot read file: {$filePath}");
        }

        $data = json_decode($json, true);
        if ($data === null) {
            throw new \RuntimeException("Invalid JSON in file: {$filePath}");
        }

        $this->db->beginTransaction();

        try {
            $this->importProviders($data['providers'] ?? []);
            $this->importModels($data['models'] ?? []);
            $this->importPrices($data['prices'] ?? []);

            $this->db->commit();
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Import price lines from structured data (used by reprice).
     * @param array $prices
     */
    public function importPriceLines(iterable $prices): void
    {
        foreach ($prices as $price) {
            $this->upsertPrice($price);
        }
    }

    private function importProviders(array $providers): void
    {
        foreach ($providers as $p) {
            $code = $p['code'];
            $name = $p['name'];
            $offPeak = $p['off_peak'] ?? null;

            $windowStart = $offPeak['window_start'] ?? null;
            $windowEnd = $offPeak['window_end'] ?? null;
            $multiplier = $offPeak['multiplier'] ?? null;

            $this->db->executeStatement(
                'INSERT INTO providers (code, name, off_peak_window_start, off_peak_window_end, off_peak_multiplier)
                 VALUES (?, ?, ?, ?, ?)
                 ON CONFLICT (code) DO UPDATE SET
                    name = EXCLUDED.name,
                    off_peak_window_start = EXCLUDED.off_peak_window_start,
                    off_peak_window_end = EXCLUDED.off_peak_window_end,
                    off_peak_multiplier = EXCLUDED.off_peak_multiplier',
                [$code, $name, $windowStart, $windowEnd, $multiplier]
            );
        }
    }

    private function importModels(array $models): void
    {
        foreach ($models as $m) {
            $provider = $m['provider'];
            $code = $m['code'];

            $this->db->executeStatement(
                'INSERT INTO models (provider_code, code)
                 VALUES (?, ?)
                 ON CONFLICT (provider_code, code) DO NOTHING',
                [$provider, $code]
            );
        }
    }

    private function importPrices(array $prices): void
    {
        foreach ($prices as $p) {
            $this->upsertPrice($p);
        }
    }

    private function upsertPrice(array $p): void
    {
        $r = $this->db->executeStatement(
            'INSERT INTO prices (provider_code, model_code, valid_from, currency, unit, rates)
             VALUES (?, ?, ?::timestamptz, ?, ?, ?)
             ON CONFLICT (provider_code, model_code, valid_from) DO UPDATE SET
                currency = EXCLUDED.currency,
                unit = EXCLUDED.unit,
                rates = EXCLUDED.rates',
            [
                $p['provider'],
                $p['model'],
                $p['valid_from'],
                $p['currency'],
                $p['unit'],
                json_encode($p['rates'], JSON_UNESCAPED_UNICODE),
            ]
        );
    }
}
