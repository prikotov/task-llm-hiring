<?php

declare(strict_types=1);

namespace App\Billing;

use App\Ingest\IngestError;

use App\Ingest\Defs\ModelDef;
use App\Ingest\Defs\PriceDef;
use App\Ingest\Defs\ProviderDef;
use Doctrine\DBAL\Connection;

/**
 * Идемпотентная загрузка справочников:
 * провайдеры — по code; модели — по (provider, code);
 * строки цен — по (provider, model, valid_from).
 */
final class CatalogService
{
    public function __construct(
        private readonly Connection $db,
    ) {
    }

    /** @param array{providers: ProviderDef[], models: ModelDef[], prices: PriceDef[]} $catalog */
    public function upsertCatalog(array $catalog): void
    {
        $conn = $this->db;

        $fileProviders = array_map(static fn (ProviderDef $p) => $p->code, $catalog['providers']);
        $fileModels = [];
        foreach ($catalog['models'] as $m) {
            $fileModels[$m->providerCode.'/'.$m->code] = true;
        }
        $dbProviders = array_fill_keys($conn->fetchFirstColumn('SELECT code FROM providers'), true);
        $dbModels = [];
        foreach ($conn->fetchAllAssociative('SELECT provider_code, code FROM models') as $row) {
            $dbModels[$row['provider_code'].'/'.$row['code']] = true;
        }

        $conn->transactional(function () use ($catalog, $conn, $fileProviders, $fileModels, $dbProviders, $dbModels): void {
            foreach ($catalog['providers'] as $p) {
                $this->upsertProvider($p, $conn);
            }

            foreach ($catalog['models'] as $i => $m) {
                if (!isset($dbProviders[$m->providerCode]) && !in_array($m->providerCode, $fileProviders, true)) {
                    throw new IngestError(sprintf('models[%d]: провайдер "%s" не найден (в файле или в базе)', $i, $m->providerCode));
                }
                $this->upsertModel($m, $conn);
            }

            foreach ($catalog['prices'] as $i => $price) {
                if (!isset($dbProviders[$price->providerCode]) && !in_array($price->providerCode, $fileProviders, true)) {
                    throw new IngestError(sprintf('prices[%d]: провайдер "%s" не найден (в файле или в базе)', $i, $price->providerCode));
                }
                $modelKey = $price->providerCode.'/'.$price->modelCode;
                if (!isset($fileModels[$modelKey]) && !isset($dbModels[$modelKey])) {
                    throw new IngestError(sprintf('prices[%d]: модель "%s" не найдена (в файле или в базе)', $i, $modelKey));
                }
                $this->upsertPriceRow($price, $conn);
            }
        });
    }

    private function upsertProvider(ProviderDef $p, Connection $conn): void
    {
        if ($conn->fetchOne('SELECT 1 FROM providers WHERE code = ?', [$p->code]) === false) {
            $conn->insert('providers', [
                'code' => $p->code,
                'name' => $p->name,
                'off_peak_window_start' => $p->offPeakStart,
                'off_peak_window_end' => $p->offPeakEnd,
                'off_peak_multiplier' => $p->offPeakMultiplier,
            ]);

            return;
        }

        $current = $conn->fetchAssociative('SELECT name, off_peak_window_start, off_peak_window_end, off_peak_multiplier FROM providers WHERE code = ?', [$p->code]);
        $changed = $current['name'] !== $p->name
            || $current['off_peak_window_start'] !== $p->offPeakStart
            || $current['off_peak_window_end'] !== $p->offPeakEnd
            || (string) ($current['off_peak_multiplier'] ?? null) !== (string) ($p->offPeakMultiplier ?? null);

        if ($changed) {
            $conn->update('providers', [
                'name' => $p->name,
                'off_peak_window_start' => $p->offPeakStart,
                'off_peak_window_end' => $p->offPeakEnd,
                'off_peak_multiplier' => $p->offPeakMultiplier,
            ], ['code' => $p->code]);
        }
    }

    private function upsertModel(ModelDef $m, Connection $conn): void
    {
        if ($conn->fetchOne('SELECT 1 FROM models WHERE provider_code = ? AND code = ?', [$m->providerCode, $m->code]) === false) {
            $conn->insert('models', ['provider_code' => $m->providerCode, 'code' => $m->code]);
        }
    }

    private function upsertPriceRow(PriceDef $price, Connection $conn): void
    {
        $data = [
            'provider_code' => $price->providerCode,
            'model_code' => $price->modelCode,
            'valid_from' => $price->validFromUtc->format('Y-m-d H:i:s'),
            'currency' => $price->currency,
            'unit' => $price->unit,
            'rate_input' => $price->rates['input'] ?? null,
            'rate_output' => $price->rates['output'] ?? null,
            'rate_cache_read' => $price->rates['cache_read'] ?? null,
            'rate_cache_write' => $price->rates['cache_write'] ?? null,
            'rate_any' => $price->rates['any'] ?? null,
        ];

        if ($conn->fetchOne(
            'SELECT 1 FROM price_rows WHERE provider_code = ? AND model_code = ? AND valid_from = ?',
            [$price->providerCode, $price->modelCode, $data['valid_from']]
        ) === false) {
            $conn->insert('price_rows', $data);

            return;
        }

        $current = $conn->fetchAssociative(
            'SELECT currency, unit, rate_input, rate_output, rate_cache_read, rate_cache_write, rate_any
             FROM price_rows WHERE provider_code = ? AND model_code = ? AND valid_from = ?',
            [$price->providerCode, $price->modelCode, $data['valid_from']]
        );
        $same = $current['currency'] === $data['currency']
            && $current['unit'] === $data['unit']
            && (string) ($current['rate_input'] ?? null) === (string) $data['rate_input']
            && (string) ($current['rate_output'] ?? null) === (string) $data['rate_output']
            && (string) ($current['rate_cache_read'] ?? null) === (string) $data['rate_cache_read']
            && (string) ($current['rate_cache_write'] ?? null) === (string) $data['rate_cache_write']
            && (string) ($current['rate_any'] ?? null) === (string) $data['rate_any'];

        if (!$same) {
            $conn->update('price_rows', [
                'currency' => $data['currency'],
                'unit' => $data['unit'],
                'rate_input' => $data['rate_input'],
                'rate_output' => $data['rate_output'],
                'rate_cache_read' => $data['rate_cache_read'],
                'rate_cache_write' => $data['rate_cache_write'],
                'rate_any' => $data['rate_any'],
            ], [
                'provider_code' => $price->providerCode,
                'model_code' => $price->modelCode,
                'valid_from' => $data['valid_from'],
            ]);
        }
    }
}
