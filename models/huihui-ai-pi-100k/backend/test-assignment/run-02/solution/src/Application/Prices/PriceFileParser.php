<?php

declare(strict_types=1);

namespace App\Application\Prices;

use App\Application\Parse\JsonInput;
use App\Domain\Pricing\PriceUnit;
use App\Exception\InvalidPricesFileException;

/**
 * Разбор файла истории цен (JSON) в PriceFileSpec.
 *
 * Правила:
 *  - верхний уровень — объект;
 *  - разделы `providers`/`models` — массивы (могут быть пустыми или
 *    отсутствовать — см. ретро-коррекции); `prices` — непустой массив;
 *  - `unit` — `per_1m_tokens` (четыре вида ставок) или
 *    `per_1m_tokens_unified` (единая ставка `any`) — ключи rates строго
 *    по схеме, лишние ключи не допускаются;
 *  - ставки — десятичные строки, `valid_from` — ISO-8601 UTC (З).
 */
final class PriceFileParser
{
    /** @throws InvalidPricesFileException */
    public function parse(string $json): PriceFileSpec
    {
        try {
            $raw = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidPricesFileException(sprintf('invalid JSON: %s', $e->getMessage()));
        }

        if (!is_array($raw)) {
            throw new InvalidPricesFileException('top-level value must be a JSON object');
        }

        foreach (['providers', 'models', 'prices'] as $key) {
            if (array_key_exists($key, $raw) && !is_array($raw[$key])) {
                throw new InvalidPricesFileException(sprintf('section "%s" must be an array', $key));
            }
        }
        if (!isset($raw['prices']) || !is_array($raw['prices']) || $raw['prices'] === []) {
            throw new InvalidPricesFileException('section "prices" is required and must contain at least one row');
        }

        $providers = array_map(
            static fn(int $i, mixed $p): ProviderSpec => self::parseProvider($p, sprintf('providers[%d]', $i)),
            array_keys($raw['providers'] ?? []),
            $raw['providers'] ?? [],
        );
        $models = array_map(
            static fn(int $i, mixed $m): ModelSpec => self::parseModel($m, sprintf('models[%d]', $i)),
            array_keys($raw['models'] ?? []),
            $raw['models'] ?? [],
        );
        $priceRows = array_map(
            static fn(int $i, mixed $r): PriceRowSpec => self::parsePriceRow($r, sprintf('prices[%d]', $i)),
            array_keys($raw['prices']),
            $raw['prices'],
        );

        return new PriceFileSpec($providers, $models, $priceRows);
    }

    /**
     * @param mixed $provider
     */
    private static function parseProvider(mixed $provider, string $ctx): ProviderSpec
    {
        if (!is_array($provider)) {
            throw new InvalidPricesFileException(sprintf('%s: must be an object', $ctx));
        }

        $code = JsonInput::requiredString($provider, 'code', $ctx, InvalidPricesFileException::class);
        $name = JsonInput::requiredString($provider, 'name', $ctx, InvalidPricesFileException::class);
        $offPeak = self::parseOffPeak($provider, $ctx);

        return new ProviderSpec($code, $name, $offPeak);
    }

    /**
     * @param mixed $provider
     */
    private static function parseOffPeak(array $provider, string $ctx): ?OffPeakSpec
    {
        if (!array_key_exists('off_peak', $provider)) {
            throw new InvalidPricesFileException(sprintf('%s: field "off_peak" is required (null or object)', $ctx));
        }
        $value = $provider['off_peak'];
        if ($value === null) {
            return null;
        }
        if (!is_array($value)) {
            throw new InvalidPricesFileException(sprintf('%s: "off_peak" must be null or an object', $ctx));
        }

        $start = JsonInput::minutesHhmm($value, 'window_start', $ctx.':off_peak', InvalidPricesFileException::class);
        $end = JsonInput::minutesHhmm($value, 'window_end', $ctx.':off_peak', InvalidPricesFileException::class);

        $multiplier = self::offPeakMultiplier($value, $ctx);

        return new OffPeakSpec($start, $end, $multiplier);
    }

    /**
     * @param array<mixed> $offPeak
     */
    private static function offPeakMultiplier(array $offPeak, string $ctx): string
    {
        $field = 'multiplier';
        if (!array_key_exists($field, $offPeak) || (!is_string($offPeak[$field]) && !is_int($offPeak[$field]) && !is_float($offPeak[$field]))) {
            throw new InvalidPricesFileException(sprintf('%s: "off_peak.%s" must be a positive decimal string', $ctx, $field));
        }

        $raw = $offPeak[$field];
        $value = is_string($raw) ? $raw : (string) $raw;
        if ($value === '' || str_contains($value, 'e') || str_contains($value, 'E') || !preg_match('/^[+-]?\d+(?:\.\d+)?$/', $value)) {
            throw new InvalidPricesFileException(sprintf('%s: "off_peak.%s" must be a positive decimal string', $ctx, $field));
        }

        if (bccomp($value, '0', 10) <= 0) {
            throw new InvalidPricesFileException(sprintf('%s: "off_peak.%s" must be > 0', $ctx, $field));
        }

        return ltrim($value, '+');
    }

    /**
     * @param mixed $model
     */
    private static function parseModel(mixed $model, string $ctx): ModelSpec
    {
        if (!is_array($model)) {
            throw new InvalidPricesFileException(sprintf('%s: must be an object', $ctx));
        }

        $provider = JsonInput::requiredString($model, 'provider', $ctx, InvalidPricesFileException::class);
        $code = JsonInput::requiredString($model, 'code', $ctx, InvalidPricesFileException::class);

        return new ModelSpec($provider, $code);
    }

    /**
     * @param mixed $row
     */
    private static function parsePriceRow(mixed $row, string $ctx): PriceRowSpec
    {
        if (!is_array($row)) {
            throw new InvalidPricesFileException(sprintf('%s: must be an object', $ctx));
        }

        $provider = JsonInput::requiredString($row, 'provider', $ctx, InvalidPricesFileException::class);
        $model = JsonInput::requiredString($row, 'model', $ctx, InvalidPricesFileException::class);
        $validFrom = JsonInput::isoUtcDateTime($row, 'valid_from', $ctx, InvalidPricesFileException::class);
        $currency = JsonInput::requiredString($row, 'currency', $ctx, InvalidPricesFileException::class);
        if (strlen($currency) < 2 || strlen($currency) > 8) {
            throw new InvalidPricesFileException(sprintf('%s: "currency" looks too long/short: "%s"', $ctx, $currency));
        }

        $unit = self::parseUnit($row, $ctx);
        $rates = self::parseRates($row, $unit, $ctx);

        return new PriceRowSpec($provider, $model, $validFrom, $currency, $unit, $rates);
    }

    /**
     * @param array<mixed> $row
     */
    private static function parseUnit(array $row, string $ctx): PriceUnit
    {
        if (!isset($row['unit']) || !is_string($row['unit'])) {
            throw new InvalidPricesFileException(sprintf('%s: field "unit" must be a string', $ctx));
        }

        try {
            return PriceUnit::fromString($row['unit']);
        } catch (\InvalidArgumentException $e) {
            throw new InvalidPricesFileException(sprintf('%s: %s', $ctx, $e->getMessage()));
        }
    }

    /**
     * @param array<mixed> $row
     *
     * @return array{input?: null|string, output?: null|string, cache_read?: null|string, cache_write?: null|string, any?: null|string}
     */
    private static function parseRates(array $row, PriceUnit $unit, string $ctx): array
    {
        if (!array_key_exists('rates', $row)) {
            throw new InvalidPricesFileException(sprintf('%s: field "rates" is required', $ctx));
        }
        $rates = $row['rates'];
        if (!is_array($rates)) {
            throw new InvalidPricesFileException(sprintf('%s: "rates" must be an object of decimal strings', $ctx));
        }

        $required = match ($unit) {
            PriceUnit::Per1mTokens => ['input', 'output', 'cache_read', 'cache_write'],
            PriceUnit::Per1mTokensUnified => ['any'],
        };

        $result = [];
        foreach ($required as $key) {
            if (!array_key_exists($key, $rates)) {
                throw new InvalidPricesFileException(sprintf('%s: "rates.%s" is required for unit "%s"', $ctx, $key, $unit->value));
            }
            $result[$key] = JsonInput::decimalString($rates, $key, $ctx, InvalidPricesFileException::class);
        }

        $expected = array_fill_keys($required, true);
        foreach (array_keys($rates) as $key) {
            if (!isset($expected[$key])) {
                throw new InvalidPricesFileException(sprintf('%s: unexpected "rates.%s" for unit "%s"', $ctx, (string) $key, $unit->value));
            }
        }

        return $result;
    }
}
