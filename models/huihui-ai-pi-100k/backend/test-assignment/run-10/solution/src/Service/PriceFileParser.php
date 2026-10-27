<?php

declare(strict_types=1);

namespace App\Service;

use App\Dto\ModelData;
use App\Dto\PriceData;
use App\Dto\ProviderData;
use App\Domain\Billing\RateUnit;
use App\Exception\InputError;

/**
 * Разбирает и валидирует файл истории цен (JSON).
 *
 * @return array{providers: ProviderData[], models: ModelData[], prices: PriceData[]}
 */
final class PriceFileParser
{
    public function parse(string $json): array
    {
        $data = json_decode($json, true, 512, flags: JSON_THROW_ON_ERROR);
        return $this->parseArray($data);
    }

    /**
     * @param mixed $data
     */
    private function parseArray($data): array
    {
        if (!is_array($data)) {
            throw InputError::because('prices: корневое значение — объект JSON');
        }

        $providers = $this->parseProviders($data['providers'] ?? []);
        $models = $this->parseModels($data['models'] ?? []);
        $prices = $this->parsePrices($data['prices'] ?? []);

        return ['providers' => $providers, 'models' => $models, 'prices' => $prices];
    }

    /**
     * @param mixed $arr
     * @return ProviderData[]
     */
    private function parseProviders($arr): array
    {
        if (!is_array($arr)) {
            throw InputError::because('prices: секция "providers" — массив');
        }

        $out = [];
        foreach ($arr as $i => $row) {
            $where = sprintf('prices.providers[%d]', $i);
            if (!is_array($row)) {
                throw InputError::because($where . ' — объект');
            }

            $code = Parsing::requireString($row, 'code', $where);
            $name = Parsing::requireString($row, 'name', $where);
            $offPeak = $this->parseOffPeak($row['off_peak'] ?? null, $where);

            $out[] = new ProviderData($code, $name, $offPeak);
        }

        return $out;
    }

    /**
     * @param mixed $value
     * @return array{window_start:string,window_end:string,multiplier:string}|null
     */
    private function parseOffPeak($value, string $where): ?array
    {
        if ($value === null) {
            return null;
        }
        if (!is_array($value)) {
            throw InputError::because($where . '.off_peak — объект или null');
        }

        $start = Parsing::requireString($value, 'window_start', $where . '.off_peak');
        $end = Parsing::requireString($value, 'window_end', $where . '.off_peak');
        $multiplier = Parsing::requireDecimalString($value, 'multiplier', $where . '.off_peak');

        // Проверяем, что HH:MM корректны.
        \App\Domain\Billing\OffPeak::minutesFromHM($start);
        \App\Domain\Billing\OffPeak::minutesFromHM($end);

        return ['window_start' => $start, 'window_end' => $end, 'multiplier' => $multiplier];
    }

    /**
     * @param mixed $arr
     * @return ModelData[]
     */
    private function parseModels($arr): array
    {
        if (!is_array($arr)) {
            throw InputError::because('prices: секция "models" — массив');
        }

        $out = [];
        foreach ($arr as $i => $row) {
            $where = sprintf('prices.models[%d]', $i);
            if (!is_array($row)) {
                throw InputError::because($where . ' — объект');
            }

            $provider = Parsing::requireString($row, 'provider', $where);
            $code = Parsing::requireString($row, 'code', $where);
            $out[] = new ModelData($provider, $code);
        }

        return $out;
    }

    /**
     * @param mixed $arr
     * @return PriceData[]
     */
    private function parsePrices($arr): array
    {
        if (!is_array($arr)) {
            throw InputError::because('prices: секция "prices" — массив');
        }

        $out = [];
        foreach ($arr as $i => $row) {
            $where = sprintf('prices.prices[%d]', $i);
            if (!is_array($row)) {
                throw InputError::because($where . ' — объект');
            }

            $provider = Parsing::requireString($row, 'provider', $where);
            $model = Parsing::requireString($row, 'model', $where);
            $validFrom = Parsing::iso8601Utc(Parsing::requireString($row, 'valid_from', $where));
            $currency = array_key_exists('currency', $row) ? (string) $row['currency'] : 'USD';
            if (!ctype_alpha($currency) || strlen($currency) !== 3) {
                throw InputError::because($where . '.currency — 3-letter код, напр. USD');
            }

            $unitCode = Parsing::requireString($row, 'unit', $where);
            try {
                $unit = RateUnit::fromCode($unitCode);
            } catch (\DomainException $e) {
                throw InputError::because($where . '.unit — неизвестная схема тарификации: ' . $e->getMessage());
            }

            $rates = $this->parseRates($row['rates'] ?? null, $unit, $where);

            $out[] = new PriceData($provider, $model, $validFrom, $unit, $rates, $currency);
        }

        return $out;
    }

    /**
     * @param mixed $raw
     * @return array<string,string>
     */
    private function parseRates($raw, RateUnit $unit, string $where): array
    {
        if (!is_array($raw)) {
            throw InputError::because($where . '.rates — объект');
        }

        try {
            $required = $unit->rateKeys();
        } catch (\DomainException $e) {
            throw InputError::because($where . '.unit — неизвестная схема: ' . $e->getMessage());
        }

        $rates = [];
        foreach ($required as $key) {
            $rates[$key] = Parsing::requireDecimalString($raw, $key, $where . '.rates');
        }

        return $rates;
    }
}
