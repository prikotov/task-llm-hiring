<?php

declare(strict_types=1);

namespace App\Application\Prices;

use App\Domain\ImportDataException;
use App\Domain\Model;
use App\Domain\PriceRow;
use App\Domain\Provider;
use App\Repository\DomainRepository;

/**
 * Импорт каталога цен (провайдеры, модели, строки цен).
 *
 * Идемпотентность по натуральным ключам:
 *  - provider  — по code;
 *  - model     — по (provider, code);
 *  - price_row — по (provider, model, valid_from): повторная загрузка
 *    тех же сумм не изменяет данных; новые суммы — заменяет строку.
 */
final class PriceImportService
{
    public function __construct(
        private readonly DomainRepository $repo,
    ) {}

    /**
     * @param array<string, mixed> $data
     */
    public function import(array $data): void
    {
        $providers = $data['providers'] ?? null;
        $models = $data['models'] ?? null;
        $prices = $data['prices'] ?? null;

        if (!is_array($providers)) {
            throw ImportDataException::new('"providers" must be an array');
        }
        if (!is_array($models)) {
            throw ImportDataException::new('"models" must be an array');
        }
        if (!is_array($prices)) {
            throw ImportDataException::new('"prices" must be an array');
        }

        $providerMap = $this->upsertProviders($providers);
        $modelMap = $this->upsertModels($models, $providerMap);
        $this->upsertPriceRows($prices, $modelMap);
    }

    /**
     * @param array<int, mixed> $providers
     *
     * @return array<string, Provider>
     */
    private function upsertProviders(array $providers): array
    {
        $map = [];

        foreach ($providers as $raw) {
            if (!is_array($raw)) {
                throw ImportDataException::new('Each provider entry must be an object');
            }

            $code = $raw['code'] ?? null;
            $name = $raw['name'] ?? null;

            if (!is_string($code) || $code === '') {
                throw ImportDataException::new('Provider needs a non-empty "code"');
            }
            if (!is_string($name) || $name === '') {
                throw ImportDataException::new("Provider '{$code}' needs a non-empty \"name\"");
            }

            $offPeak = self::parseOffPeak($raw['off_peak'] ?? null, $code);
            $existing = $this->repo->findProvider($code);

            if ($existing === null) {
                $provider = new Provider($code, $name);
            } else {
                $provider = $existing;
            }

            $provider
                ->setOffPeakStartMinutes($offPeak['start_minutes']);
            $provider
                ->setOffPeakEndMinutes($offPeak['end_minutes']);
            $provider
                ->setOffPeakMultiplier($offPeak['multiplier']);

            $this->repo->saveProvider($provider);
            $map[$code] = $provider;
        }

        return $map;
    }

    /**
     * @param array<int, mixed>        $models
     * @param array<string, Provider>  $providerMap
     *
     * @return array<string, Model>
     */
    private function upsertModels(array $models, array $providerMap): array
    {
        $map = [];

        foreach ($models as $raw) {
            if (!is_array($raw)) {
                throw ImportDataException::new('Each model entry must be an object');
            }

            $pc = $raw['provider'] ?? null;
            $mc = $raw['code'] ?? null;

            if (!is_string($pc) || $pc === '') {
                throw ImportDataException::new('Model needs a non-empty "provider"');
            }
            if (!is_string($mc) || $mc === '') {
                throw ImportDataException::new('Model needs a non-empty "code"');
            }

            $key = $pc.'.'.$mc;

            if (isset($map[$key])) {
                continue;
            }

            if (!isset($providerMap[$pc])) {
                throw ImportDataException::new("Unknown provider '{$pc}'; it must be in the \"providers\" array");
            }

            $provider = $providerMap[$pc];
            $existing = $this->repo->findModel($pc, $mc);

            if ($existing !== null) {
                $map[$key] = $existing;
                continue;
            }

            $model = new Model($mc, $provider);
            $this->repo->saveModel($model);
            $map[$key] = $model;
        }

        return $map;
    }

    /**
     * @param array<int, mixed> $prices
     * @param array<string, Model> $modelMap
     */
    private function upsertPriceRows(array $prices, array $modelMap): void
    {
        foreach ($prices as $raw) {
            if (!is_array($raw)) {
                throw ImportDataException::new('Each price entry must be an object');
            }

            $pc = $raw['provider'] ?? null;
            $mc = $raw['model'] ?? null;
            $validFromRaw = $raw['valid_from'] ?? null;
            $currency = $raw['currency'] ?? null;
            $unit = $raw['unit'] ?? null;
            $rates = $raw['rates'] ?? null;

            if (!is_string($pc) || $pc === '') {
                throw ImportDataException::new('Price row needs a non-empty "provider"');
            }
            if (!is_string($mc) || $mc === '') {
                throw ImportDataException::new('Price row needs a non-empty "model"');
            }
            if (!is_string($validFromRaw) || $validFromRaw === '') {
                throw ImportDataException::new('Price row needs a valid ISO-8601 UTC "valid_from"');
            }
            if (!is_string($unit) || $unit === '') {
                throw ImportDataException::new('Price row needs a non-empty "unit"');
            }
            if (!is_string($currency) || $currency === '') {
                throw ImportDataException::new('Price row needs a non-empty "currency"');
            }
            if (!is_array($rates)) {
                throw ImportDataException::new('Price row needs "rates" (object with decimal-string values)');
            }

            $key = $pc.'.'.$mc;

            if (!isset($modelMap[$key])) {
                throw ImportDataException::new("Unknown model '{$pc}/{$mc}'; it must be in the \"models\" array");
            }

            $model = $modelMap[$key];
            $provider = $model->getProvider();
            $validFrom = self::parseInstant($validFromRaw);
            $ratesNormalized = self::validateRates($rates, $unit);

            $row = new PriceRow(
                validFrom: $validFrom,
                currency: $currency,
                unit: $unit,
                rates: $ratesNormalized,
                model: $model,
            );

            $this->repo->upsertPriceRow($row);
        }
    }

    public static function parseInstant(string $iso): \DateTimeImmutable
    {
        try {
            $dt = new \DateTimeImmutable($iso);
        } catch (\Exception) {
            throw ImportDataException::new("Unparseable timestamp '{$iso}'");
        }

        return $dt;
    }

    /**
     * @param array<string, mixed>|null $offPeak
     *
     * @return ?array{start_minutes: ?int, end_minutes: ?int, multiplier: ?string}
     */
    private static function parseOffPeak(?array $offPeak, string $providerCode): array
    {
        $default = ['start_minutes' => null, 'end_minutes' => null, 'multiplier' => null];

        if ($offPeak === null) {
            return $default;
        }

        $startMinutes = self::parseHHmm($offPeak['window_start'] ?? null, "off_peak.window_start (provider \"{$providerCode}\")");
        $endMinutes = self::parseHHmm($offPeak['window_end'] ?? null, "off_peak.window_end (provider \"{$providerCode}\")");
        $multRaw = $offPeak['multiplier'] ?? null;

        if (!is_string($multRaw) || !preg_match('/^\d+(\.\d+)?$/', $multRaw)) {
            throw ImportDataException::new('off_peak.multiplier (provider "'.$providerCode.'") must be a non-negative decimal string');
        }

        return [
            'start_minutes' => $startMinutes,
            'end_minutes' => $endMinutes,
            'multiplier' => $multRaw,
        ];
    }

    private static function parseHHmm(mixed $hhmm, string $field): int
    {
        if (!is_string($hhmm) || !preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $hhmm)) {
            throw ImportDataException::new($field.' must be HH:MM (00:00–23:59)');
        }
        [$h, $m] = explode(':', $hhmm);
        return ((int) $h) * 60 + (int) $m;
    }

    /**
     * @param array<string, mixed> $rates
     *
     * @return array<string, string>
     */
    private static function validateRates(array $rates, string $unit): array
    {
        $required = match ($unit) {
            PriceRow::UNIT_PER_1M => ['input', 'output', 'cache_read', 'cache_write'],
            PriceRow::UNIT_PER_1M_UNIFIED => ['any'],
            default => throw new ImportDataException("Unknown unit '{$unit}'"),
        };

        $result = [];

        foreach ($required as $key) {
            $value = $rates[$key] ?? null;

            if ($value === null) {
                throw ImportDataException::new("Missing rate '{$key}' for unit '{$unit}'");
            }
            if (is_int($value) || is_float($value)) {
                $value = (string) $value;
            }
            if (!is_string($value) || !preg_match('/^\d+(\.\d+)?$/', $value)) {
                throw ImportDataException::new("Rate '{$key}' must be a non-negative decimal string");
            }
            $result[$key] = $value;
        }

        return $result;
    }
}
