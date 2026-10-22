<?php

declare(strict_types=1);

namespace App\Import;

use App\Domain\Pricing\PriceEntry;
use App\Shared\ImportException;

/**
 * Decodes and validates a price catalog JSON (app:prices:import file, or a
 * price correction file for app:billing:reprice).
 */
final class PricesCatalogParser
{
    public const MODE_IMPORT = 'import';
    public const MODE_REPRICE = 'reprice';

    /**
     * @param array<mixed> $decoded json_decode result (associative array)
     */
    public function parse(array $decoded, string $mode): ParsedCatalog
    {
        if ($mode === self::MODE_IMPORT) {
            $providers = $this->parseProviders($decoded['providers'] ?? [], 'providers');
            $models = $this->parseModels($decoded['models'] ?? [], 'models', $providers);

            if (isset($decoded['prices']) && !\is_array($decoded['prices'])) {
                throw new ImportException('"prices" must be an array');
            }
        } else {
            if (!isset($decoded['prices']) || !\is_array($decoded['prices']) || $decoded['prices'] === []) {
                throw new ImportException('"prices" must be a non-empty array (reprice file)');
            }

            $providers = [];
            $models = [];
        }

        $prices = [];
        foreach ($decoded['prices'] ?? [] as $i => $row) {
            $prices[] = $this->parsePriceRow($row, (string) $i, $mode, $providers, $models);
        }

        return new ParsedCatalog($providers, $models, $prices);
    }

    /** @param array<mixed> $raw */
    private function parseProviders(array $raw, string $where): array
    {
        $providers = [];
        $seenCodes = [];

        foreach ($raw as $i => $row) {
            if (!\is_array($row)) {
                throw new ImportException(\sprintf('%s[%d] must be an object', $where, $i));
            }

            $code = $this->nonEmptyString($row['code'] ?? null, \sprintf('%s[%d].code', $where, $i));
            $name = $this->nonEmptyString($row['name'] ?? null, \sprintf('%s[%d].name', $where, $i));

            if (isset($seenCodes[$code])) {
                throw new ImportException(\sprintf('%s[%d]: duplicate provider code "%s"', $where, $i, $code));
            }
            $seenCodes[$code] = true;

            $offPeak = null;
            if (array_key_exists('off_peak', $row) && $row['off_peak'] !== null) {
                if (!\is_array($row['off_peak'])) {
                    throw new ImportException(\sprintf('%s[%d].off_peak must be an object or null', $where, $i));
                }
                $op = $row['off_peak'];
                if (\array_diff_key($op, array_flip(['window_start', 'window_end', 'multiplier'])) !== []) {
                    throw new ImportException(\sprintf('%s[%d].off_peak has unknown fields', $where, $i));
                }
                foreach (['window_start', 'window_end', 'multiplier'] as $key) {
                    if (!\array_key_exists($key, $op)) {
                        throw new ImportException(\sprintf('%s[%d].off_peak.%s is required', $where, $i, $key));
                    }
                }
                $offPeak = [
                    'window_start' => $this->windowTime($op['window_start'], \sprintf('%s[%d].off_peak.window_start', $where, $i)),
                    'window_end' => $this->windowTime($op['window_end'], \sprintf('%s[%d].off_peak.window_end', $where, $i)),
                    'multiplier' => $this->decimal($op['multiplier'], \sprintf('%s[%d].off_peak.multiplier', $where, $i), positive: true),
                ];
            }

            $providers[$code] = new ProviderData($code, $name, $offPeak);
        }

        return $providers;
    }

    /**
     * @param array<mixed> $raw
     * @param array<string, ProviderData> $providers
     *
     * @return array<string, array{code: string}> models by provider code
     */
    private function parseModels(array $raw, string $where, array $providers): array
    {
        $models = [];
        $seen = [];

        foreach ($raw as $i => $row) {
            if (!\is_array($row)) {
                throw new ImportException(\sprintf('%s[%d] must be an object', $where, $i));
            }

            $provider = $this->nonEmptyString($row['provider'] ?? null, \sprintf('%s[%d].provider', $where, $i));
            $code = $this->nonEmptyString($row['code'] ?? null, \sprintf('%s[%d].code', $where, $i));

            if (!isset($providers[$provider])) {
                throw new ImportException(\sprintf('%s[%d]: unknown provider "%s"', $where, $i, $provider));
            }

            $key = $provider . '/' . $code;
            if (isset($seen[$key])) {
                throw new ImportException(\sprintf('%s[%d]: duplicate model "%s"', $where, $i, $key));
            }
            $seen[$key] = true;

            $models[$provider][$code] = ['code' => $code];
        }

        return $models;
    }

    /**
     * @param array<mixed>|mixed $row
     * @param array<string, ProviderData> $providers
     * @param array<string, array{code: string}> $models
     */
    private function parsePriceRow($row, string $i, string $mode, array $providers, array $models): PriceData
    {
        $where = \sprintf('prices[%s]', $i);

        if (!\is_array($row)) {
            throw new ImportException($where . ' must be an object');
        }

        $provider = $this->nonEmptyString($row['provider'] ?? null, $where . '.provider');
        $model = $this->nonEmptyString($row['model'] ?? null, $where . '.model');

        if ($mode === self::MODE_IMPORT) {
            if (!isset($providers[$provider])) {
                throw new ImportException(\sprintf('%s: unknown provider "%s"', $where, $provider));
            }
            if (!isset($models[$provider][$model])) {
                throw new ImportException(\sprintf('%s: unknown model "%s/%s"', $where, $provider, $model));
            }
        }

        $validFrom = $this->isoUtc($row['valid_from'] ?? null, $where . '.valid_from');
        $currency = $this->nonEmptyString($row['currency'] ?? null, $where . '.currency');
        $unit = $this->nonEmptyString($row['unit'] ?? null, $where . '.unit');
        $rates = $this->parseRates($row['rates'] ?? null, $where, $unit);

        // Validate scheme/rate pairing (throws ImportException on mismatch).
        new PriceEntry($unit, $rates);

        return new PriceData($provider, $model, $validFrom, $currency, $unit, $rates);
    }

    /**
     * @param array<mixed>|null $rates
     *
     * @return array<string, string>
     */
    private function parseRates(?array $rates, string $where, string $unit): array
    {
        if (!\is_array($rates)) {
            throw new ImportException($where . '.rates must be an object');
        }

        $keys = match ($unit) {
            'per_1m_tokens' => ['input', 'output', 'cache_read', 'cache_write'],
            'per_1m_tokens_unified' => ['any'],
            default => throw new ImportException(\sprintf('%s: unknown billing unit "%s"', $where, $unit)),
        };

        $result = [];
        foreach ($keys as $key) {
            if (!\array_key_exists($key, $rates)) {
                throw new ImportException(\sprintf('%s.rates.%s is required for unit "%s"', $where, $key, $unit));
            }
            $result[$key] = $this->decimal($rates[$key], \sprintf('%s.rates.%s', $where, $key));
        }

        $unknown = \array_diff_key($rates, array_flip($keys));
        if ($unknown !== []) {
            throw new ImportException(\sprintf('%s.rates has unknown fields for unit "%s": %s', $where, $unit, \implode(', ', \array_keys($unknown))));
        }

        return $result;
    }

    private function nonEmptyString(mixed $value, string $where): string
    {
        if (!\is_string($value) || $value === '') {
            throw new ImportException(\sprintf('%s must be a non-empty string', $where));
        }

        return $value;
    }

    /**
     * @param out string positive decimal string, 1..6 digits after the point
     * @param string $where
     * @param bool $positive require value > 0
     */
    private function decimal(mixed $value, string $where, bool $positive = false): string
    {
        if (!\is_string($value) || \preg_match('/^\d+(\.\d{1,6})?$/', $value) !== 1) {
            throw new ImportException(\sprintf('%s must be a decimal string with at most 6 digits after the point', $where));
        }
        if ($positive && \bccomp($value, '0', 6) === 0) {
            throw new ImportException(\sprintf('%s must be greater than zero', $where));
        }

        return $value;
    }

    private function windowTime(mixed $value, string $where): string
    {
        if (!\is_string($value) || \preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $value) !== 1) {
            throw new ImportException(\sprintf('%s must be an "HH:MM" (UTC) string', $where));
        }

        return $value;
    }

    private function isoUtc(mixed $value, string $where): \DateTimeImmutable
    {
        if (!\is_string($value) || \preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?Z$/', $value) !== 1) {
            throw new ImportException(\sprintf('%s must be ISO-8601 UTC, e.g. "2026-08-01T00:00:00Z"', $where));
        }

        $dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $value);
        if ($dt === false) {
            $dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s.u\Z', $value);
        }

        if ($dt === false) {
            throw new ImportException(\sprintf('%s is not a valid date-time: "%s"', $where, $value));
        }

        return $dt;
    }
}
