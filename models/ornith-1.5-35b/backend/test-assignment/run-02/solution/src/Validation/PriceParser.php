<?php

declare(strict_types=1);

namespace App\Validation;

use App\Exception\InvalidJsonlLineException;
use App\Exception\InvalidJsonException;

/**
 * Parses and validates the price index JSON and the usage JSONL into plain
 * structures. Throws on any malformed input so callers can map the failure to a
 * proper exit code.
 */
final class PriceParser
{
    /**
     * @return array{providers: list<array<string,mixed>>, models: list<array<string,string>>, prices: list<array<string,mixed>>}
     */
    public function parsePrices(string $file): array
    {
        $raw = $this->readJson($file);
        if (!\is_array($raw)) {
            throw new InvalidJsonException('Prices file must decode to a JSON object');
        }
        if (!array_key_exists('providers', $raw)) {
            throw new InvalidJsonException('Prices file must contain a "providers" array');
        }
        if (!array_key_exists('models', $raw)) {
            throw new InvalidJsonException('Prices file must contain a "models" array');
        }
        if (!array_key_exists('prices', $raw)) {
            throw new InvalidJsonException('Prices file must contain a "prices" array');
        }

        $providers = $this->validateProviders($raw['providers']);
        $models = $this->validateModels($raw['models']);
        $prices = $this->validatePrices($raw['prices']);

        return ['providers' => $providers, 'models' => $models, 'prices' => $prices];
    }

    /**
     * @return list<array<string,mixed>>
     */
    public function parseUsageFile(string $file): array
    {
        if (!is_file($file)) {
            throw new \RuntimeException('Usage file not found: '.$file);
        }
        $content = file_get_contents($file);
        if (false === $content) {
            throw new \RuntimeException('Cannot read usage file: '.$file);
        }

        $records = [];
        $lines = preg_split("/\r\n|\r|\n/", $content) ?: [];
        $lineNo = 0;
        foreach ($lines as $line) {
            ++$lineNo;
            $trimmed = trim($line);
            if ('' === $trimmed) {
                continue;
            }
            $records[] = $this->validateUsageLine($trimmed, $lineNo);
        }

        return $records;
    }

    /**
     * @param mixed $providers
     * @return list<array<string,mixed>>
     */
    private function validateProviders(mixed $providers): array
    {
        if (!\is_array($providers) || !array_is_list($providers)) {
            throw new InvalidJsonException('"providers" must be a JSON array');
        }
        $out = [];
        $codes = [];
        foreach ($providers as $i => $provider) {
            if (!\is_array($provider)) {
                throw new InvalidJsonException('Provider #'.$i.' must be an object');
            }
            $code = $this->requireString($provider, 'code', 'Provider #'.$i);
            $name = $this->requireString($provider, 'name', 'Provider #'.$i);
            if ('' === $code || '' === $name) {
                throw new InvalidJsonException('Provider code and name must be non-empty');
            }
            if (isset($codes[$code])) {
                throw new InvalidJsonException('Duplicate provider code "'.$code.'"');
            }
            $codes[$code] = true;
            $offPeak = $provider['off_peak'] ?? null;
            $out[] = ['code' => $code, 'name' => $name, 'off_peak' => $this->validateOffPeak($offPeak, $code)];
        }

        return $out;
    }

    /**
     * @param array<string,mixed>|null $offPeak
     * @return array<string,mixed>|null
     */
    private function validateOffPeak(mixed $offPeak, string $code): ?array
    {
        if (null === $offPeak) {
            return null;
        }
        if (!\is_array($offPeak)) {
            throw new InvalidJsonException('Provider '.$code.' off_peak must be null or an object');
        }
        foreach (['window_start', 'window_end', 'multiplier'] as $field) {
            if (!array_key_exists($field, $offPeak)) {
                throw new InvalidJsonException('Provider '.$code.' off_peak requires "'.$field.'"');
            }
            if (!\is_string($offPeak[$field]) || '' === trim($offPeak[$field])) {
                throw new InvalidJsonException('Provider '.$code.' off_peak.'.$field.' must be a non-empty string');
            }
        }
        if (!str_contains((string) $offPeak['window_start'], ':') || !str_contains((string) $offPeak['window_end'], ':')) {
            throw new InvalidJsonException('Provider '.$code.' off_peak windows must be in HH:MM format');
        }
        $mw = trim($offPeak['multiplier']);
        if (1 !== preg_match('/^-?(0|[1-9]\d*)(\.\d+)?$/', $mw)) {
            throw new InvalidJsonException('Provider '.$code.' off_peak.multiplier must be a decimal');
        }

        return ['window_start' => $offPeak['window_start'], 'window_end' => $offPeak['window_end'], 'multiplier' => $mw];
    }

    /**
     * @param mixed $models
     * @return list<array<string,string>>
     */
    private function validateModels(mixed $models): array
    {
        if (!\is_array($models) || !array_is_list($models)) {
            throw new InvalidJsonException('"models" must be a JSON array');
        }
        $out = [];
        foreach ($models as $i => $model) {
            if (!\is_array($model)) {
                throw new InvalidJsonException('Model #'.$i.' must be an object');
            }
            $provider = $this->requireString($model, 'provider', 'Model #'.$i);
            $code = $this->requireString($model, 'code', 'Model #'.$i);
            if ('' === $provider || '' === $code) {
                throw new InvalidJsonException('Model provider and code must be non-empty');
            }
            $out[] = ['provider' => $provider, 'code' => $code];
        }

        return $out;
    }

    /**
     * @param mixed $prices
     * @return list<array<string,mixed>>
     */
    private function validatePrices(mixed $prices): array
    {
        if (!\is_array($prices) || !array_is_list($prices)) {
            throw new InvalidJsonException('"prices" must be a JSON array');
        }
        $out = [];
        foreach ($prices as $i => $price) {
            if (!\is_array($price)) {
                throw new InvalidJsonException('Price #'.$i.' must be an object');
            }
            $provider = $this->requireString($price, 'provider', 'Price #'.$i);
            $model = $this->requireString($price, 'model', 'Price #'.$i);
            $validFrom = $this->requireString($price, 'valid_from', 'Price #'.$i);
            $unit = $this->requireString($price, 'unit', 'Price #'.$i);
            $rates = $price['rates'] ?? null;
            if (!\is_array($rates) || (!array_is_list($rates) && !array_key_exists('any', $rates))) {
                throw new InvalidJsonException('Price '.$i.' rates must be an object of decimal strings');
            }
            foreach ($rates as $key => $value) {
                if (!\is_string($value) || '' === trim($value)) {
                    throw new InvalidJsonException('Price '.$i.' rate "'.$key.'" must be a non-empty string decimal');
                }
            }
            $perM = $unit === \App\Domain\Entity\ModelPrice::UNIT_PER_M;
            if ($perM) {
                foreach (['input', 'output', 'cache_read', 'cache_write'] as $field) {
                    if (!array_key_exists($field, $rates)) {
                        throw new InvalidJsonException('Price '.$i.' needs rate "'.$field.'" for per_1m_tokens unit');
                    }
                }
            }
            $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i:sP', $validFrom);
            if (false === $parsed) {
                throw new InvalidJsonException('Price '.$i.' has invalid valid_from "'.$validFrom.'"');
            }
            $out[] = ['provider' => $provider, 'model' => $model, 'valid_from' => $validFrom, 'unit' => $unit, 'rates' => $rates];
        }

        return $out;
    }

    /**
     * @return array<string,mixed>
     */
    private function validateUsageLine(string $line, int $lineNo): array
    {
        $data = json_decode($line, true);
        if (!\is_array($data)) {
            throw new InvalidJsonlLineException($lineNo, $line);
        }
        $provider = $this->requireString($data, 'provider', 'Line '.$lineNo);
        $providerRequestId = $this->requireString($data, 'provider_request_id', 'Line '.$lineNo);
        $model = $this->requireString($data, 'model', 'Line '.$lineNo);
        $user = $this->requireString($data, 'user', 'Line '.$lineNo);
        $ts = $this->requireString($data, 'ts', 'Line '.$lineNo);
        $parsed = \DateTimeImmutable::createFromFormat('!Y-m-d\\TH:i:sP', $ts);
        if (false === $parsed) {
            throw new InvalidJsonlLineException($lineNo, $line);
        }
        $input = $this->nonNegativeInt($data, 'tokens_input');
        $output = $this->nonNegativeInt($data, 'tokens_output');
        $cacheRead = $this->nonNegativeInt($data, 'tokens_cache_read');
        $cacheWrite = $this->nonNegativeInt($data, 'tokens_cache_write');

        return ['provider' => $provider, 'provider_request_id' => $providerRequestId, 'model' => $model, 'user' => $user, 'ts' => $ts, 'tokens_input' => $input, 'tokens_output' => $output, 'tokens_cache_read' => $cacheRead, 'tokens_cache_write' => $cacheWrite];
    }

    private function requireString(array $data, string $field, string $label): string
    {
        if (!array_key_exists($field, $data)) {
            throw new InvalidJsonException($label.' is missing "'.$field.'"');
        }
        if (!\is_string($data[$field])) {
            throw new InvalidJsonException($label.' field "'.$field.'" must be a string');
        }
        $value = trim($data[$field]);
        if ('' === $value) {
            throw new InvalidJsonException($label.' field "$field" must be non-empty');
        }

        return $value;
    }

    private function nonNegativeInt(array $data, string $field): int
    {
        if (!array_key_exists($field, $data)) {
            return 0;
        }
        $value = $data[$field];
        if (!\is_int($value) || $value < 0) {
            throw new InvalidJsonlLineException($lineNo, $line);
        }

        return $value;
    }

    private function readJson(string $file): mixed
    {
        if (!is_file($file)) {
            throw new \RuntimeException('File not found: '.$file);
        }
        $content = file_get_contents($file);
        if (false === $content) {
            throw new \RuntimeException('Cannot read file: '.$file);
        }
        $data = json_decode($content, true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new InvalidJsonException('Invalid JSON in '.$file.': '.json_last_error_msg());
        }

        return $data;
    }
}
