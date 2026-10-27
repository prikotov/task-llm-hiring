<?php

declare(strict_types=1);

namespace App\Application\Ingestion;

use App\Domain\Billing\Cost;
use App\Domain\DomainException;
use App\Domain\Pricing\BillingUnit;
use App\Domain\Pricing\OffPeakPolicy;
use App\Domain\Pricing\StoredPriceRow;

/**
 * Разбор и валидация JSON-справочника цен.
 *
 * Результат — объект {@see PriceCatalogInput} с готовыми
 * `StoredPriceRow`-сущностями, которые можно сразу записывать в БД.
 *
 * @throws DomainException при неверном JSON, полях или значениях
 */
final class PriceCatalogParser
{
    /** @throws DomainException */
    public function parse(string $json, string $context = 'PriceCatalogParser::parse'): PriceCatalogInput
    {
        if ($json === '') {
            throw new DomainException("{$context}: empty JSON document");
        }
        $data = json_decode($json, true);
        if (!is_array($data)) {
            throw new DomainException("{$context}: JSON document is not an object");
        }

        $providers = $this->parseProviders($data['providers'] ?? [], $context);
        $models = $this->parseModels($data['models'] ?? [], $context);
        $rows = $this->parsePrices($data['prices'] ?? [], $providers, $context);

        return new PriceCatalogInput($providers, $models, $rows);
    }

    /**
     * @param mixed $items
     * @return array<string, array{name:string, off_peak:?OffPeakPolicy}>
     * @throws DomainException
     */
    private function parseProviders(mixed $items, string $ctx): array
    {
        if (!is_array($items)) {
            throw new DomainException("{$ctx}: providers must be an array");
        }
        $out = [];
        foreach ($items as $i => $item) {
            if (!is_array($item)) {
                throw new DomainException("{$ctx}: providers[{$i}] must be an object");
            }
            $code = self::string($item['code'] ?? null, "{$ctx}: providers[{$i}].code");
            $name = self::string($item['name'] ?? $code, "{$ctx}: providers[{$i}].name", allowDefault: true);

            $offPeak = null;
            $raw = $item['off_peak'] ?? null;
            if ($raw !== null && $raw !== false) {
                if (!is_array($raw)) {
                    throw new DomainException("{$ctx}: providers[{$i}].off_peak must be an object or null");
                }
                $ws = self::string($raw['window_start'] ?? null, "{$ctx}: providers[{$i}].off_peak.window_start");
                $we = self::string($raw['window_end'] ?? null, "{$ctx}: providers[{$i}].off_peak.window_end");
                $mul = self::string($raw['multiplier'] ?? null, "{$ctx}: providers[{$i}].off_peak.multiplier");
                self::assertTimeOfDay($ws, "{$ctx}: providers[{$i}].off_peak.window_start");
                self::assertTimeOfDay($we, "{$ctx}: providers[{$i}].off_peak.window_end");
                self::assertDecimalSix($mul, "{$ctx}: providers[{$i}].off_peak.multiplier");
                $offPeak = new OffPeakPolicy(
                    windowStart: $ws,
                    windowEnd: $we,
                    multiplier: Cost::make($mul),
                );
            }

            $out[$code] = ['name' => $name, 'off_peak' => $offPeak];
        }

        return $out;
    }

    /**
     * @param mixed $items
     * @return array<int, array{provider:string, code:string}>
     * @throws DomainException
     */
    private function parseModels(mixed $items, string $ctx): array
    {
        if (!is_array($items)) {
            throw new DomainException("{$ctx}: models must be an array");
        }
        $out = [];
        foreach ($items as $i => $item) {
            if (!is_array($item)) {
                throw new DomainException("{$ctx}: models[{$i}] must be an object");
            }
            $prov = self::string($item['provider'] ?? null, "{$ctx}: models[{$i}].provider");
            $code = self::string($item['code'] ?? null, "{$ctx}: models[{$i}].code");
            $out[] = ['provider' => $prov, 'code' => $code];
        }

        return $out;
    }

    /**
     * @param mixed $items
     * @return StoredPriceRow[]
     * @throws DomainException
     */
    private function parsePrices(mixed $items, array $providers, string $ctx): array
    {
        if (!is_array($items)) {
            throw new DomainException("{$ctx}: prices must be an array");
        }
        $rows = [];
        foreach ($items as $i => $item) {
            if (!is_array($item)) {
                throw new DomainException("{$ctx}: prices[{$i}] must be an object");
            }
            $prov = self::string($item['provider'] ?? null, "{$ctx}: prices[{$i}].provider");
            $model = self::string($item['model'] ?? null, "{$ctx}: prices[{$i}].model");
            $vf = self::string($item['valid_from'] ?? null, "{$ctx}: prices[{$i}].valid_from");
            $unitStr = self::string($item['unit'] ?? null, "{$ctx}: prices[{$i}].unit");

            if ($providers !== [] && !isset($providers[$prov])) {
                throw new DomainException("{$ctx}: prices[{$i}]: unknown provider \"{$prov}\"");
            }

            $unit = BillingUnit::tryFrom($unitStr)
                ?? throw new DomainException("{$ctx}: prices[{$i}]: unknown unit \"{$unitStr}\"");

            $rates = $item['rates'] ?? null;
            if (!is_array($rates)) {
                throw new DomainException("{$ctx}: prices[{$i}].rates must be an object");
            }

            $expected = $unit === BillingUnit::PER_1M_TOKENS
                ? ['input', 'output', 'cache_read', 'cache_write']
                : ['any'];

            $rateCosts = [];
            foreach ($expected as $r) {
                if (!array_key_exists($r, $rates)) {
                    throw new DomainException("{$ctx}: prices[{$i}].rates[{$r}] is missing");
                }
                $v = self::string($rates[$r], "{$ctx}: prices[{$i}].rates[{$r}]");
                self::assertDecimalSix($v, "{$ctx}: prices[{$i}].rates[{$r}]");
                $rateCosts[$r] = Cost::make($v);
            }

            $rows[] = StoredPriceRow::create(
                provider: $prov,
                model: $model,
                validFrom: self::isoUtc($vf, "{$ctx}: prices[{$i}].valid_from"),
                currency: self::string($item['currency'] ?? null, "{$ctx}: prices[{$i}].currency", allowDefault: true) ?: 'USD',
                unit: $unit,
                rates: $rateCosts,
            );
        }

        return $rows;
    }

    // --- helpers ---

    private static function string(mixed $v, string $ctx, bool $allowDefault = false): string
    {
        if ($v === null) {
            if ($allowDefault) {
                return '';
            }
            throw new DomainException("{$ctx}: required but missing");
        }
        if (!is_string($v) || $v === '') {
            throw new DomainException("{$ctx}: must be a non-empty string");
        }

        return $v;
    }

    private static function isoUtc(string $v, string $ctx): \DateTimeImmutable
    {
        if (substr($v, -1) !== 'Z') {
            throw new DomainException("{$ctx}: must be ISO-8601 UTC (ending with 'Z'): \"{$v}\"");
        }
        try {
            $date = new \DateTimeImmutable($v);
        } catch (\DateTimeParseException) {
            throw new DomainException("{$ctx}: not a valid ISO-8601 timestamp: \"{$v}\"");
        }
        if ($date->getOffset() !== 0) {
            throw new DomainException("{$ctx}: UTC required (offset must be 0): \"{$v}\"");
        }

        return $date;
    }

    private static function assertDecimalSix(string $v, string $ctx): void
    {
        if (!is_numeric($v) || \is_infinite((float) $v) || \is_nan((float) $v)) {
            throw new DomainException("{$ctx}: must be a finite decimal number: \"{$v}\"");
        }
        // до 6 знаков после точки (0.5 OK, 0.1234567 нет)
        if (!preg_match('/^-?\d+(\.\d{1,6})?$/', $v)) {
            throw new DomainException("{$ctx}: must have ≤6 fractional digits: \"{$v}\"");
        }
    }

    private static function assertTimeOfDay(string $v, string $ctx): void
    {
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $v)) {
            throw new DomainException("{$ctx}: must be HH:MM (00:00–23:59): \"{$v}\"");
        }
    }
}
