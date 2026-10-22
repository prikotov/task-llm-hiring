<?php

declare(strict_types=1);

namespace App\Service;

use Doctrine\DBAL\Connection;
use InvalidArgumentException;

final class PriceImporter
{
    public function __construct(private readonly Connection $connection)
    {
    }

    public function import(string $file): int
    {
        $contents = @file_get_contents($file);
        if ($contents === false) {
            throw new InvalidArgumentException(sprintf('Не удалось прочитать файл "%s"', $file));
        }
        $document = InputData::decodeObject($contents, $file);
        $providers = $document['providers'] ?? [];
        $models = $document['models'] ?? [];
        $prices = $document['prices'] ?? [];
        foreach (['providers' => $providers, 'models' => $models, 'prices' => $prices] as $key => $value) {
            if (!is_array($value) || !array_is_list($value)) {
                throw new InvalidArgumentException(sprintf('%s: поле "%s" должно быть JSON-массивом', $file, $key));
            }
        }
        if (!array_key_exists('providers', $document) || !array_key_exists('models', $document) || !array_key_exists('prices', $document)) {
            throw new InvalidArgumentException(sprintf('%s: нужны секции providers, models и prices', $file));
        }

        $this->connection->beginTransaction();
        try {
            foreach ($providers as $index => $provider) {
                $context = sprintf('%s providers[%d]', $file, $index);
                if (!is_array($provider)) {
                    throw new InvalidArgumentException(sprintf('%s: ожидается объект', $context));
                }
                $code = InputData::requiredString($provider, 'code', $context);
                $name = InputData::requiredString($provider, 'name', $context);
                if (!array_key_exists('off_peak', $provider)) {
                    throw new InvalidArgumentException(sprintf('%s: отсутствует поле "off_peak"', $context));
                }
                $offPeak = $provider['off_peak'];
                if ($offPeak !== null && !is_array($offPeak)) {
                    throw new InvalidArgumentException(sprintf('%s: off_peak должен быть объектом или null', $context));
                }
                if ($offPeak === null) {
                    $start = $end = $multiplier = null;
                } else {
                    $start = InputData::timeOfDay($offPeak['window_start'] ?? null, $context.' off_peak.window_start');
                    $end = InputData::timeOfDay($offPeak['window_end'] ?? null, $context.' off_peak.window_end');
                    $multiplier = InputData::decimal($offPeak['multiplier'] ?? null, $context.' off_peak.multiplier');
                }

                $this->connection->executeStatement(<<<'SQL'
INSERT INTO providers (code, name, off_peak_start, off_peak_end, off_peak_multiplier)
VALUES (:code, :name, :start, :end, :multiplier)
ON CONFLICT (code) DO UPDATE SET name = EXCLUDED.name,
    off_peak_start = EXCLUDED.off_peak_start, off_peak_end = EXCLUDED.off_peak_end,
    off_peak_multiplier = EXCLUDED.off_peak_multiplier
WHERE (providers.name, providers.off_peak_start, providers.off_peak_end, providers.off_peak_multiplier)
    IS DISTINCT FROM (EXCLUDED.name, EXCLUDED.off_peak_start, EXCLUDED.off_peak_end, EXCLUDED.off_peak_multiplier)
SQL, ['code' => $code, 'name' => $name, 'start' => $start, 'end' => $end, 'multiplier' => $multiplier]);
            }

            foreach ($models as $index => $model) {
                $context = sprintf('%s models[%d]', $file, $index);
                if (!is_array($model)) {
                    throw new InvalidArgumentException(sprintf('%s: ожидается объект', $context));
                }
                $provider = InputData::requiredString($model, 'provider', $context);
                $code = InputData::requiredString($model, 'code', $context);
                if (!$this->connection->fetchOne('SELECT 1 FROM providers WHERE code = :code', ['code' => $provider])) {
                    throw new InvalidArgumentException(sprintf('%s: провайдер "%s" отсутствует в базе', $context, $provider));
                }
                $this->connection->executeStatement(<<<'SQL'
INSERT INTO provider_models (provider_code, code) VALUES (:provider, :code)
ON CONFLICT (provider_code, code) DO NOTHING
SQL, ['provider' => $provider, 'code' => $code]);
            }

            $changed = 0;
            foreach ($prices as $index => $record) {
                if (!is_array($record)) {
                    throw new InvalidArgumentException(sprintf('%s prices[%d]: ожидается объект', $file, $index));
                }
                $data = $this->parsePrice($record, sprintf('%s prices[%d]', $file, $index));
                if (!$this->connection->fetchOne('SELECT 1 FROM provider_models WHERE provider_code = :provider AND code = :model', ['provider' => $data['provider'], 'model' => $data['model']])) {
                    throw new InvalidArgumentException(sprintf('%s prices[%d]: модель "%s/%s" отсутствует в справочнике', $file, $index, $data['provider'], $data['model']));
                }
                $changed += $this->connection->executeStatement(<<<'SQL'
INSERT INTO prices (
    provider_code, model_code, valid_from, currency, unit,
    rate_input, rate_output, rate_cache_read, rate_cache_write, rate_any
) VALUES (
    :provider, :model, :valid_from, :currency, :unit,
    :rate_input, :rate_output, :rate_cache_read, :rate_cache_write, :rate_any
)
ON CONFLICT (provider_code, model_code, valid_from) DO UPDATE SET
    currency = EXCLUDED.currency, unit = EXCLUDED.unit,
    rate_input = EXCLUDED.rate_input, rate_output = EXCLUDED.rate_output,
    rate_cache_read = EXCLUDED.rate_cache_read, rate_cache_write = EXCLUDED.rate_cache_write,
    rate_any = EXCLUDED.rate_any
WHERE (prices.currency, prices.unit, prices.rate_input, prices.rate_output,
       prices.rate_cache_read, prices.rate_cache_write, prices.rate_any)
    IS DISTINCT FROM (EXCLUDED.currency, EXCLUDED.unit, EXCLUDED.rate_input, EXCLUDED.rate_output,
                      EXCLUDED.rate_cache_read, EXCLUDED.rate_cache_write, EXCLUDED.rate_any)
SQL, $data);
            }
            $this->connection->commit();
            return $changed;
        } catch (\Throwable $exception) {
            $this->connection->rollBack();
            throw $exception;
        }
    }

    /** @param array<string, mixed> $record
     *  @return array<string, mixed>
     */
    public function parsePrice(array $record, string $context): array
    {
        $provider = InputData::requiredString($record, 'provider', $context);
        $model = InputData::requiredString($record, 'model', $context);
        $validFrom = InputData::sqlTimestamp(InputData::utcTimestamp($record['valid_from'] ?? null, $context.' valid_from'));
        $currency = InputData::requiredString($record, 'currency', $context);
        if ($currency !== 'USD') {
            throw new InvalidArgumentException(sprintf('%s: поддерживается только USD', $context));
        }
        $unit = InputData::requiredString($record, 'unit', $context);
        $rates = $record['rates'] ?? null;
        if (!is_array($rates)) {
            throw new InvalidArgumentException(sprintf('%s: rates должен быть объектом', $context));
        }

        $columns = ['rate_input' => null, 'rate_output' => null, 'rate_cache_read' => null, 'rate_cache_write' => null, 'rate_any' => null];
        if ($unit === 'per_1m_tokens') {
            foreach (['input', 'output', 'cache_read', 'cache_write'] as $kind) {
                $columns['rate_'.$kind] = InputData::decimal($rates[$kind] ?? null, $context.' rates.'.$kind, 6);
            }
        } elseif ($unit === 'per_1m_tokens_unified') {
            $columns['rate_any'] = InputData::decimal($rates['any'] ?? null, $context.' rates.any', 6);
        } else {
            throw new InvalidArgumentException(sprintf('%s: неизвестная схема тарификации "%s"', $context, $unit));
        }

        return [
            'provider' => $provider,
            'model' => $model,
            'valid_from' => $validFrom,
            'currency' => $currency,
            'unit' => $unit,
            ...$columns,
        ];
    }
}
