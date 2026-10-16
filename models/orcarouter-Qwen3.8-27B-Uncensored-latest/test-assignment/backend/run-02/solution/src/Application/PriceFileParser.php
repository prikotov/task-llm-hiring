<?php

declare(strict_types=1);

namespace App\Application;

use App\Domain\OffPeakWindow;
use App\Domain\TokenUsage;
use App\Exception\ImportException;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Разбор и валидация файла со справочником цен (JSON).
 * Result — нормализованные массивы со строковыми decimal-значениями.
 *
 * @return array{providers: array, models: array, prices: array}
 */
final class PriceFileParser
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';
    public const MODE_IMPORT = 'import';
    public const MODE_REPRICE = 'reprice';

    /**
     * @throws ImportException
     */
    public static function parse(string $raw, string $mode = self::MODE_IMPORT): array
    {
        try {
            $data = json_decode($raw, true, 512, \JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException('invalid JSON: '.$e->getMessage());
        }
        if (!\is_array($data)) {
            throw new ImportException('root must be an object');
        }

        foreach (['providers', 'models'] as $section) {
            if (!\array_key_exists($section, $data)) {
                $data[$section] = [];
            }
            if (!\is_array($data[$section])) {
                throw new ImportException(sprintf('section "%s" must be an array', $section));
            }
        }
        if (!\is_array($data['prices'] ?? null)) {
            throw new ImportException('section "prices" must be an array');
        }

        $providers = [];
        foreach ($data['providers'] as $i => $p) {
            $providers[] = [
                'code' => self::requireString($p, 'code', "providers[$i]"),
                'name' => self::requireString($p, 'name', "providers[$i]"),
                'off_peak' => self::parseOffPeak($p, $i),
            ];
        }

        $models = [];
        foreach ($data['models'] as $i => $m) {
            $models[] = [
                'provider' => self::requireString($m, 'provider', "models[$i]"),
                'code' => self::requireString($m, 'code', "models[$i]"),
            ];
        }

        $prices = [];
        foreach ($data['prices'] as $i => $p) {
            $unit = self::requireString($p, 'unit', "prices[$i]");
            $prices[] = [
                'provider' => self::requireString($p, 'provider', "prices[$i]"),
                'model' => self::requireString($p, 'model', "prices[$i]"),
                'valid_from' => self::parseValidFrom($p, $i),
                'currency' => self::requireString($p, 'currency', "prices[$i]"),
                'unit' => $unit,
                'rates' => self::parseRates($p, $unit, $i),
            ];
        }

        if ($mode === self::MODE_REPRICE && $prices === []) {
            throw new ImportException('prices: correction file must contain at least one price line');
        }

        return ['providers' => $providers, 'models' => $models, 'prices' => $prices];
    }

    private static function parseOffPeak(array $p, int $i): ?OffPeakWindow
    {
        if (!\array_key_exists('off_peak', $p)) {
            return null;
        }
        $raw = $p['off_peak'];
        if ($raw === null) {
            return null;
        }
        if (!\is_array($raw)) {
            throw new ImportException(sprintf('providers[%d].off_peak: must be an object or null', $i));
        }

        $start = self::requireString($raw, 'window_start', "providers[$i].off_peak");
        $end = self::requireString($raw, 'window_end', "providers[$i].off_peak");
        $multiplier = self::requireDecimal($raw, 'multiplier', "providers[$i].off_peak");

        return OffPeakWindow::fromRaw($start, $end, $multiplier);
    }

    /** @param array<string, mixed> $rates */
    private static function parseRates(array $p, string $unit, int $i): array
    {
        if (!\is_array($p['rates'] ?? null)) {
            throw new ImportException(sprintf('prices[%d].rates: must be an object', $i));
        }

        return match ($unit) {
            self::UNIT_PER_TOKEN => self::parseRatesByKind($p['rates'], $i, [
                'input', 'output', 'cache_read', 'cache_write',
            ]),
            self::UNIT_UNIFIED => self::parseRatesByKind($p['rates'], $i, ['any']),
            default => throw new ImportException(
                sprintf('prices[%d].unit: unknown billing unit "%s"', $i, $unit)
            ),
        };
    }

    /**
     * @param array<string, mixed> $rates
     * @param string[] $requiredKinds
     * @return array<string, string> kind => десятичная строка
     */
    private static function parseRatesByKind(array $rates, int $i, array $requiredKinds): array
    {
        $result = [];
        foreach ($requiredKinds as $kind) {
            if (!\array_key_exists($kind, $rates)) {
                throw new ImportException(sprintf('prices[%d].rates.%s: missing', $i, $kind));
            }
            $result[$kind] = self::requireDecimal($rates, $kind, "prices[$i].rates");
        }
        foreach (\array_keys($rates) as $key) {
            if (!\in_array($key, $requiredKinds, true)) {
                throw new ImportException(sprintf('prices[%d].rates: unknown token kind "%s"', $i, (string) $key));
            }
        }

        return $result;
    }

    private static function parseValidFrom(array $p, int $i): string
    {
        $raw = $p['valid_from'] ?? null;
        if (!\is_string($raw) || $raw === '') {
            throw new ImportException(sprintf('prices[%d].valid_from: must be an ISO-8601 string', $i));
        }
        try {
            return (new DateTimeImmutable($raw))
                ->setTimezone(new DateTimeZone('UTC'))
                ->format('Y-m-d H:i:s');
        } catch (\Exception) {
            throw new ImportException(sprintf('prices[%d].valid_from: invalid ISO-8601 date "%s"', $i, $raw));
        }
    }

    private static function requireString(array $source, string $key, string $ctx): string
    {
        $value = $source[$key] ?? null;
        if (!\is_string($value) || $value === '') {
            throw new ImportException(sprintf('%s.%s: must be a non-empty string', $ctx, $key));
        }

        return $value;
    }

    private static function requireDecimal(array $source, string $key, string $ctx): string
    {
        $value = $source[$key] ?? null;
        if (\is_int($value)) {
            $normalized = (string) $value;
        } elseif (\is_float($value) && is_finite($value)) {
            $normalized = (string) $value;
        } elseif (\is_string($value)) {
            $normalized = $value;
        } else {
            throw new ImportException(sprintf('%s.%s: must be a decimal number or string', $ctx, $key));
        }

        if (!preg_match('/^\d+(\.\d+)?$/', $normalized) || (float) $normalized < 0) {
            throw new ImportException(
                sprintf('%s.%s: must be a non-negative decimal, got "%s"', $ctx, $key, $normalized)
            );
        }

        return $normalized;
    }
}
