<?php

declare(strict_types=1);

namespace App\Application\Catalog;

use App\Domain\Catalog\PricingUnit;
use App\Exception\DataImportException;

/**
 * Разбор и валидация структуры файла истории цен (JSON).
 * Общий для импорта каталога и ретро-коррекции (reprice).
 */
final class CatalogInput
{
    public const MAX_RATE_DECIMALS = 6;

    /**
     * Читает JSON-файл справочника.
     *
     * @return array<string, mixed>
     */
    public static function readJson(string $path): array
    {
        if (is_file($path) === false || is_readable($path) === false) {
            throw new DataImportException(sprintf('File is not readable: %s', $path));
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new DataImportException(sprintf('Cannot read file: %s', $path));
        }

        try {
            $data = json_decode($raw, true, 64, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new DataImportException(sprintf('Invalid JSON in %s: %s', $path, $e->getMessage()));
        }

        if (!is_array($data)) {
            throw new DataImportException(sprintf('Top-level JSON value must be an object in %s.', $path));
        }

        return $data;
    }

    /**
     * Секция providers: list of [code, name, ?offPeakStart, ?offPeakEnd, ?offPeakMultiplier].
     *
     * @param array<string, mixed> $data
     *
     * @return list<array{0: string, 1: string, 2: ?string, 3: ?string, 4: ?string}>
     */
    public static function parseProviders(array $data, string $file): array
    {
        $items = self::section($data, 'providers', $file);
        $result = [];
        foreach ($items as $i => $item) {
            if (!is_array($item)) {
                throw new DataImportException(sprintf('%s: providers[%d] must be an object.', $file, $i));
            }

            $code = self::nonEmptyString($item['code'] ?? null, sprintf('%s: providers[%d].code', $file, $i));
            $name = self::nonEmptyString($item['name'] ?? null, sprintf('%s: providers[%d].name', $file, $i));

            $offPeak = $item['off_peak'] ?? null;
            if ($offPeak === null) {
                $result[] = [$code, $name, null, null, null];

                continue;
            }

            if (!is_array($offPeak)) {
                throw new DataImportException(sprintf('%s: providers[%d].off_peak must be an object or null.', $file, $i));
            }

            $start = self::nonEmptyString($offPeak['window_start'] ?? null, sprintf('%s: providers[%d].off_peak.window_start', $file, $i));
            $end = self::nonEmptyString($offPeak['window_end'] ?? null, sprintf('%s: providers[%d].off_peak.window_end', $file, $i));
            $multiplier = self::nonEmptyString($offPeak['multiplier'] ?? null, sprintf('%s: providers[%d].off_peak.multiplier', $file, $i));

            foreach (['window_start' => $start, 'window_end' => $end] as $field => $value) {
                if (preg_match('/^(?:[01]\d|2[0-3]):[0-5]\d$/', $value) !== 1) {
                    throw new DataImportException(sprintf(
                        '%s: providers[%d].off_peak.%s must be "HH:MM", got "%s".',
                        $file, $i, $field, $value,
                    ));
                }
            }

            self::assertDecimal($multiplier, sprintf('%s: providers[%d].off_peak.multiplier', $file, $i));

            $result[] = [$code, $name, $start, $end, $multiplier];
        }

        return $result;
    }

    /**
     * Секция models: list of [provider, code].
     *
     * @param array<string, mixed> $data
     *
     * @return list<array{0: string, 1: string}>
     */
    public static function parseModels(array $data, string $file): array
    {
        $items = self::section($data, 'models', $file);
        $result = [];
        foreach ($items as $i => $item) {
            if (!is_array($item)) {
                throw new DataImportException(sprintf('%s: models[%d] must be an object.', $file, $i));
            }

            $provider = self::nonEmptyString($item['provider'] ?? null, sprintf('%s: models[%d].provider', $file, $i));
            $code = self::nonEmptyString($item['code'] ?? null, sprintf('%s: models[%d].code', $file, $i));

            $result[] = [$provider, $code];
        }

        return $result;
    }

    /**
     * Секция prices.
     *
     * @param array<string, mixed> $data
     *
     * @return list<array{
     *     provider: string, model: string, validFrom: \DateTimeImmutable,
     *     currency: string, unit: PricingUnit, rates: array<string, string>
     * }>
     */
    public static function parsePrices(array $data, string $file): array
    {
        $items = self::section($data, 'prices', $file);
        $result = [];
        foreach ($items as $i => $item) {
            if (!is_array($item)) {
                throw new DataImportException(sprintf('%s: prices[%d] must be an object.', $file, $i));
            }

            $ctx = sprintf('%s: prices[%d]', $file, $i);
            $provider = self::nonEmptyString($item['provider'] ?? null, $ctx.'.provider');
            $model = self::nonEmptyString($item['model'] ?? null, $ctx.'.model');
            $validFrom = self::parseUtcTimestamp(
                self::nonEmptyString($item['valid_from'] ?? null, $ctx.'.valid_from'),
                $ctx.'.valid_from',
            );
            $currency = self::nonEmptyString($item['currency'] ?? null, $ctx.'.currency');
            $unitRaw = self::nonEmptyString($item['unit'] ?? null, $ctx.'.unit');

            try {
                $unit = PricingUnit::from($unitRaw);
            } catch (\ValueError) {
                throw new DataImportException(sprintf(
                    '%s: unknown pricing unit "%s" (expected per_1m_tokens or per_1m_tokens_unified).',
                    $ctx, $unitRaw,
                ));
            }

            $ratesRaw = $item['rates'] ?? null;
            if (!is_array($ratesRaw)) {
                throw new DataImportException(sprintf('%s: rates must be an object.', $ctx));
            }

            $expected = $unit->expectedRateKeys();
            $rates = [];
            foreach ($expected as $kind) {
                if (!array_key_exists($kind, $ratesRaw)) {
                    throw new DataImportException(sprintf('%s: rates.%s is required for unit %s.', $ctx, $kind, $unitRaw));
                }

                $value = self::nonEmptyString($ratesRaw[$kind], $ctx.".rates.{$kind}");
                self::assertRateDecimal($value, $ctx.".rates.{$kind}");
                $rates[$kind] = $value;
            }

            if (array_diff(array_keys($ratesRaw), $expected) !== []) {
                throw new DataImportException(sprintf(
                    '%s: rates contains unknown keys (expected exactly: %s).',
                    $ctx,
                    implode(', ', $expected),
                ));
            }

            $result[] = [
                'provider' => $provider,
                'model' => $model,
                'validFrom' => $validFrom,
                'currency' => $currency,
                'unit' => $unit,
                'rates' => $rates,
            ];
        }

        return $result;
    }

    /**
     * ISO-8601 момент, принудительно интерпретируемый в UTC (по условию — всегда Z).
     */
    public static function parseUtcTimestamp(string $value, string $context): \DateTimeImmutable
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d{1,6})?(Z|[+-]\d{2}:?\d{2})$/i', $value) !== 1) {
            throw new DataImportException(sprintf('%s: expected ISO-8601 UTC timestamp, got "%s".', $context, $value));
        }

        $dt = new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        if ($dt === null) {
            throw new DataImportException(sprintf('%s: cannot parse timestamp "%s".', $context, $value));
        }

        return $dt;
    }

    /**
     * @param mixed $value
     *
     * @return list<mixed>
     */
    private static function section(array $data, string $name, string $file): array
    {
        if (!array_key_exists($name, $data)) {
            return [];
        }

        $value = $data[$name];
        if (!is_array($value) || array_is_list($value) === false) {
            throw new DataImportException(sprintf('%s: section "%s" must be a JSON array.', $file, $name));
        }

        return $value;
    }

    /**
     * @param mixed $value
     */
    private static function nonEmptyString(mixed $value, string $context): string
    {
        if (!is_string($value) || $value === '') {
            throw new DataImportException(sprintf('%s: expected a non-empty string.', $context));
        }

        return $value;
    }

    private static function assertDecimal(string $value, string $context): void
    {
        if (preg_match('/^-?\d+(\.\d+)?$/', $value) !== 1) {
            throw new DataImportException(sprintf('%s: expected a decimal string, got "%s".', $context, $value));
        }
    }

    /**
     * Цена — неотрицательная десятичная строка, не более 6 знаков после точки.
     */
    private static function assertRateDecimal(string $value, string $context): void
    {
        if (preg_match('/^\d+(\.\d{1,'.self::MAX_RATE_DECIMALS.'})?$/', $value) !== 1) {
            throw new DataImportException(sprintf(
                '%s: expected a non-negative decimal string with up to %d decimals, got "%s".',
                $context,
                self::MAX_RATE_DECIMALS,
                $value,
            ));
        }
    }
}
