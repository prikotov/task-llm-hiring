<?php

declare(strict_types=1);

namespace App\Billing\Parser;

use App\Billing\Entity\Model;
use App\Billing\Entity\PriceRow;
use App\Billing\Entity\Provider;
use App\Billing\Store\Store;
use App\Domain\Billing\Money;
use App\Domain\Billing\PricingScheme;
use InvalidArgumentException;
use JsonException;

/**
 * Парсинг и валидация JSON файла цен (TASK.md § «История цен»).
 */
final class PricesCatalogLoader
{
    public function __construct(
        private readonly Store $store,
    ) {}

    /**
     * Upsert справочника (провайдеры, модели, строки цен). Идемпотентный.
     * Возвращает статистику для stderr — «OK» или «UNCHANGED».
     *
     * @return array{changed_providers:int, changed_models:int, changed_prices:int}
     */
    public function load(string $path): array
    {
        $raw = @file_get_contents($path);
        if ($raw === false) {
            throw new \RuntimeException(sprintf('Cannot read file: %s', $path));
        }

        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new \RuntimeException('Invalid JSON: ' . $e->getMessage(), 0, $e);
        }

        if (!is_array($data)) {
            throw new \RuntimeException('Top-level JSON must be an object with keys providers/models/prices.');
        }

        $stats = ['changed_providers' => 0, 'changed_models' => 0, 'changed_prices' => 0];

        $providers = $data['providers'] ?? [];
        foreach ($providers as $p) {
            $this->upsertProvider($p);
            $stats['changed_providers']++;
        }

        $models = $data['models'] ?? [];
        foreach ($models as $m) {
            $this->upsertModel($m);
            $stats['changed_models']++;
        }

        $priceRows = $data['prices'] ?? [];
        foreach ($priceRows as $r) {
            $this->upsertPriceRow($r);
            $stats['changed_prices']++;
        }

        $this->store->flush();

        return $stats;
    }

    /** @param array<string, mixed> $p */
    private function upsertProvider(array $p): void
    {
        $code = self::stringField($p, 'code');
        $name = self::stringField($p, 'name');

        $offPeak = $p['off_peak'] ?? null;
        $startMinutes = null;
        $endMinutes = null;
        $multiplier = null;

        if ($offPeak === null) {
            // Off-peak не задан
        } elseif (!is_array($offPeak)) {
            throw new \RuntimeException(sprintf('Provider "%s": off_peak must be an object or null.', $code));
        } else {
            $windowStart = (string) ($offPeak['window_start'] ?? '');
            $windowEnd = (string) ($offPeak['window_end'] ?? '');
            $multStr = (string) ($offPeak['multiplier'] ?? '');
            $startMinutes = self::minutesOfDay($windowStart);
            $endMinutes = self::minutesOfDay($windowEnd);
            $multiplier = self::decimalString($multStr);
            if ($multiplier === null || bccomp($multiplier, '0', 18) <= 0) {
                throw new \RuntimeException(sprintf('Provider "%s": off_peak.multiplier must be a positive decimal string.', $code));
            }
            if ($startMinutes === $endMinutes) {
                throw new \RuntimeException(sprintf('Provider "%s": off_peak window start and end must differ.', $code));
            }
        }

        $provider = new Provider($code);
        $provider->name = $name;
        $provider->offPeakStartMinutes = $startMinutes;
        $provider->offPeakEndMinutes = $endMinutes;
        $provider->offPeakMultiplier = $multiplier;
        $this->store->upsertProvider($provider);
    }

    /** @param array<string, mixed> $m */
    private function upsertModel(array $m): void
    {
        $provider = self::stringField($m, 'provider');
        $code = self::stringField($m, 'code');
        if ($this->store->provider($provider) === null) {
            throw new \RuntimeException(sprintf('Model provider "%s" not found (expected from prices catalog).', $provider));
        }

        $id = \App\Billing\Entity\EntityId::model($provider, $code);
        $model = new Model($id);
        $model->providerCode = $provider;
        $model->code = $code;
        $this->store->upsertModel($model);
    }

    /** @param array<string, mixed> $r */
    private function upsertPriceRow(array $r): void
    {
        $provider = self::stringField($r, 'provider');
        $model = self::stringField($r, 'model');
        $validFromRaw = self::stringField($r, 'valid_from');
        try {
            $validFrom = new \DateTimeImmutable($validFromRaw);
        } catch (\Exception $e) {
            throw new \RuntimeException(sprintf('Invalid valid_from "%s".', $validFromRaw), 0, $e);
        }
        if ($validFrom->getTimezone() !== (new \DateTimeImmutable('2026-01-01T00:00:00Z'))->getTimezone()) {
            $validFrom = $validFrom->setTimezone(new \DateTimeZone('UTC'));
        }

        $currency = self::stringField($r, 'currency');
        if ($currency !== 'USD') {
            throw new \RuntimeException(sprintf('Only USD currency is supported (got "%s").', $currency));
        }

        $unit = self::stringField($r, 'unit');
        try {
            $scheme = PricingScheme::parse($unit);
        } catch (\InvalidArgumentException) {
            throw new \RuntimeException(sprintf('Unknown unit "%s".', $unit));
        }

        $rates = $r['rates'] ?? null;
        if (!is_array($rates) || $rates === []) {
            throw new \RuntimeException('Rates must be a non-empty object.');
        }

        $rateMap = [];
        foreach ($rates as $k => $v) {
            if (!is_string($v) && !is_int($v)) {
                throw new \RuntimeException(sprintf('Rate for key "%s" must be a decimal string.', $k));
            }
            $d = self::decimalString((string) $v);
            if ($d === null) {
                throw new \RuntimeException(sprintf('Rate for key "%s" is not a positive decimal.', $k));
            }
            if (bccomp($d, '0', 18) < 0) {
                throw new \RuntimeException(sprintf('Rate for key "%s" must be non-negative.', $k));
            }
            $rateMap[(string) $k] = $d;
        }

        // Валидация ключей по схеме
        switch ($scheme) {
            case PricingScheme::PER_TOKEN:
                $required = ['input', 'output', 'cache_read', 'cache_write'];
                if (array_diff($required, array_keys($rateMap)) !== []) {
                    throw new \RuntimeException('Unit per_1m_tokens requires rates input/output/cache_read/cache_write.');
                }
                break;
            case PricingScheme::PER_TOKEN_UNIFIED:
                if (!isset($rateMap['any'])) {
                    throw new \RuntimeException('Unit per_1m_tokens_unified requires rate "any".');
                }
                break;
        }

        $id = \App\Billing\Entity\EntityId::priceRow($provider, $model, $validFrom);
        $row = new PriceRow($id);
        $row->providerCode = $provider;
        $row->modelCode = $model;
        $row->validFrom = $validFrom;
        $row->currency = $currency;
        $row->unit = $unit;
        $row->rates = $rateMap;
        $this->store->upsertPriceRow($row);
    }

    // helpers

    /** @return ?string нормализованная десятичная строка */
    private static function decimalString(string $v): ?string
    {
        $v = trim($v);
        if ($v === '' || $v === null) {
            return null;
        }
        if (!preg_match('/^-?\d+(?:\.\d+)?$/', $v)) {
            return null;
        }
        // канонизируем (bcmath)
        return bccomp($v, '0', 18) === 0 ? '0' : bcadd($v, '0', 18);
    }

    private static function minutesOfDay(string $hhmm): int
    {
        if (!preg_match('/^(\d{1,2}):(\d{2})$/', $hhmm, $m)) {
            throw new \RuntimeException(sprintf('Invalid off-peak time format: "%s" (expected "HH:MM").', $hhmm));
        }
        $h = (int) $m[1];
        $mi = (int) $m[2];
        if ($h > 23 || $mi > 59) {
            throw new \RuntimeException(sprintf('Invalid off-peak time: "%s".', $hhmm));
        }

        return $h * 60 + $mi;
    }

    /** @param array<string, mixed> $a */
    private static function stringField(array $a, string $key): string
    {
        $v = $a[$key] ?? null;
        if (!is_string($v) || $v === '') {
            throw new \RuntimeException(sprintf('Field "%s" is required and must be a non-empty string.', $key));
        }

        return $v;
    }
}
