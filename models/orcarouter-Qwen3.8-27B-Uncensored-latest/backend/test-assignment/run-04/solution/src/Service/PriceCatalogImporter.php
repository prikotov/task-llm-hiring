<?php

declare(strict_types=1);

namespace App\Service;

use App\Import\ParsedCatalog;
use App\Import\PricesCatalogParser;
use App\Shared\ImportException;
use Doctrine\DBAL\Connection;

/**
 * Upserts a price catalog into the reference tables. Idempotent: providers by
 * code, models by (provider, code), price rows by (provider, model, valid_from).
 */
final class PriceCatalogImporter
{
    public function __construct(
        private readonly Connection $db,
        private readonly PricesCatalogParser $parser,
    ) {
    }

    /** @return array{providers: int, models: int, prices: int} */
    public function importFile(string $path, string $mode = PricesCatalogParser::MODE_IMPORT): array
    {
        $decoded = self::readJsonFile($path);
        $catalog = $this->parser->parse($decoded, $mode);

        return $this->db->transactional(function () use ($catalog): array {
            $counts = ['providers' => 0, 'models' => 0, 'prices' => 0];

            foreach ($catalog->providers as $provider) {
                $this->db->executeStatement(
                    'INSERT INTO providers (code, name, off_peak_window_start, off_peak_window_end, off_peak_multiplier)
                     VALUES (:code, :name, :ws, :we, :mult)
                     ON CONFLICT (code) DO UPDATE SET
                        name = EXCLUDED.name,
                        off_peak_window_start = EXCLUDED.off_peak_window_start,
                        off_peak_window_end = EXCLUDED.off_peak_window_end,
                        off_peak_multiplier = EXCLUDED.off_peak_multiplier',
                    [
                        'code' => $provider->code,
                        'name' => $provider->name,
                        'ws' => $provider->offPeak['window_start'] ?? null,
                        'we' => $provider->offPeak['window_end'] ?? null,
                        'mult' => $provider->offPeak['multiplier'] ?? null,
                    ],
                );
                ++$counts['providers'];
            }

            foreach ($catalog->modelsByProvider as $provider => $models) {
                foreach ($models as $code => $_) {
                    $this->db->executeStatement(
                        'INSERT INTO models (provider, code) VALUES (:provider, :code)
                         ON CONFLICT (provider, code) DO NOTHING',
                        ['provider' => $provider, 'code' => $code],
                    );
                    ++$counts['models'];
                }
            }

            foreach ($catalog->prices as $price) {
                $this->db->executeStatement(
                    'INSERT INTO price_rows (provider, model, valid_from, currency, unit, rate_input, rate_output, rate_cache_read, rate_cache_write, rate_any)
                     VALUES (:provider, :model, :valid_from, :currency, :unit, :ri, :ro, :rc, :rw, :ra)
                     ON CONFLICT (provider, model, valid_from) DO UPDATE SET
                        currency = EXCLUDED.currency,
                        unit = EXCLUDED.unit,
                        rate_input = EXCLUDED.rate_input,
                        rate_output = EXCLUDED.rate_output,
                        rate_cache_read = EXCLUDED.rate_cache_read,
                        rate_cache_write = EXCLUDED.rate_cache_write,
                        rate_any = EXCLUDED.rate_any',
                    [
                        'provider' => $price->provider,
                        'model' => $price->model,
                        'valid_from' => $price->validFrom->format('Y-m-d H:i:sO'),
                        'currency' => $price->currency,
                        'unit' => $price->unit,
                        'ri' => $price->rates['input'] ?? null,
                        'ro' => $price->rates['output'] ?? null,
                        'rc' => $price->rates['cache_read'] ?? null,
                        'rw' => $price->rates['cache_write'] ?? null,
                        'ra' => $price->rates['any'] ?? null,
                    ],
                );
                ++$counts['prices'];
            }

            return $counts;
        });
    }

    /** @return array<mixed> */
    public static function readJsonFile(string $path): array
    {
        if (!\is_file($path) || !\is_readable($path)) {
            throw ImportException::forFile($path, 'file not found or not readable');
        }

        $raw = \file_get_contents($path);
        if ($raw === false) {
            throw ImportException::forFile($path, 'cannot read file');
        }

        $decoded = \json_decode($raw, true);
        if (\json_last_error() !== \JSON_ERROR_NONE || !\is_array($decoded)) {
            throw ImportException::forFile($path, \sprintf('invalid JSON: %s', \json_last_error_msg()));
        }

        return $decoded;
    }
}
