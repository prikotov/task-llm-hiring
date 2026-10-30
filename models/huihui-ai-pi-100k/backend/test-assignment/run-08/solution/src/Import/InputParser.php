<?php

declare(strict_types=1);

namespace App\Import;

use DateTimeImmutable;

/** Исключение некорректного входного файла/строки (→ exit 1). */
final class InputFormatException extends \RuntimeException
{
}

final class InputParser
{
    public const UNIT_PER_1M = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    private const PER_1M_RATES = ['input', 'output', 'cache_read', 'cache_write'];
    private const DECIMAL_RE = '/^(0|[1-9]\d*)(\.\d{1,6})?$/';
    private const WINDOW_RE = '/^([01]\d|2[0-3]):[0-5]\d$/';

    /**
     * Разбирает и валидирует JSON-файл истории цен
     * (формат: providers[]/models[]/prices[]).
     *
     * @return array{
     *   providers: array<array{code:string,name:string,off_peak:?array{start:string,end:string,multiplier:string}}>,
     *   models: array<array{provider:string,code:string}>,
     *   prices: array<array{provider:string,model:string,valid_from:DateTimeImmutable,currency:string,unit:string,rates:array<string,string>}>,
     * }
     *
     * @throws InputFormatException
     */
    public static function priceFile(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new InputFormatException(sprintf('File not found or unreadable: %s', $path));
        }

        $raw = file_get_contents($path);
        try {
            $json = json_decode($raw ?? '', true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InputFormatException(sprintf('Invalid JSON in %s — %s', $path, $e->getMessage()), previous: $e);
        }
        if (!\is_array($json)) {
            throw new InputFormatException('Root of prices file must be a JSON object.');
        }
        if (array_is_list($json)) {
            throw new InputFormatException('Root of prices file must be a JSON object, not a list.');
        }

        return [
            'providers' => self::parseProviders($json),
            'models' => self::parseModels($json),
            'prices' => self::parsePrices($json),
        ];
    }

    /** @param array<string,mixed> $json */
    private static function parseProviders(array $json): array
    {
        if (!\array_key_exists('providers', $json) || $json['providers'] === null) {
            return [];
        }
        if (!\is_array($json['providers'])) {
            throw new InputFormatException('«providers» must be an array.');
        }

        $seen = [];
        $out = [];
        foreach ($json['providers'] as $i => $row) {
            self::assertAssoc($row, sprintf('providers[%d]', $i));
            $code = self::nonEmptyString($row['code'] ?? null, sprintf('providers[%d].code', $i));
            $name = self::nonEmptyString($row['name'] ?? null, sprintf('providers[%d].name', $i));
            $offPeak = self::parseOffPeak($row['off_peak'] ?? null, sprintf('providers[%d].off_peak', $i));

            if (isset($seen[$code])) {
                throw new InputFormatException(sprintf('Duplicate provider code "%s" (providers[%d]).', $code, $i));
            }
            $seen[$code] = true;

            $out[] = ['code' => $code, 'name' => $name, 'off_peak' => $offPeak];
        }

        return $out;
    }

    /**
     * @param mixed $value
     * @return array{start:string,end:string,multiplier:string}|null
     */
    private static function parseOffPeak(mixed $value, string $path): ?array
    {
        if ($value === null) {
            return null;
        }
        if (!\is_array($value)) {
            throw new InputFormatException(sprintf('%s must be object or null.', $path));
        }

        $start = self::window($value['window_start'] ?? null, $path.'.window_start');
        $end = self::window($value['window_end'] ?? null, $path.'.window_end');
        $multiplier = self::decimalString($value['multiplier'] ?? null, $path.'.multiplier');

        return ['start' => $start, 'end' => $end, 'multiplier' => $multiplier];
    }

    private static function window(mixed $value, string $path): string
    {
        if (!\is_string($value) || !preg_match(self::WINDOW_RE, $value)) {
            throw new InputFormatException(sprintf('%s must be "HH:MM".', $path));
        }

        return $value;
    }

    /** @param array<string,mixed> $json */
    private static function parseModels(array $json): array
    {
        if (!\array_key_exists('models', $json) || $json['models'] === null) {
            return [];
        }
        if (!\is_array($json['models'])) {
            throw new InputFormatException('«models» must be an array.');
        }

        $seen = [];
        $out = [];
        foreach ($json['models'] as $i => $row) {
            self::assertAssoc($row, sprintf('models[%d]', $i));
            $provider = self::nonEmptyString($row['provider'] ?? null, sprintf('models[%d].provider', $i));
            $code = self::nonEmptyString($row['code'] ?? null, sprintf('models[%d].code', $i));

            $key = $provider.'|'.$code;
            if (isset($seen[$key])) {
                throw new InputFormatException(sprintf('Duplicate model %s (models[%d]).', $key, $i));
            }
            $seen[$key] = true;

            $out[] = ['provider' => $provider, 'code' => $code];
        }

        return $out;
    }

    /** @param array<string,mixed> $json */
    private static function parsePrices(array $json): array
    {
        if (!\array_key_exists('prices', $json) || $json['prices'] === null) {
            return [];
        }
        if (!\is_array($json['prices'])) {
            throw new InputFormatException('«prices» must be an array.');
        }

        $out = [];
        foreach ($json['prices'] as $i => $row) {
            self::assertAssoc($row, sprintf('prices[%d]', $i));
            $provider = self::nonEmptyString($row['provider'] ?? null, sprintf('prices[%d].provider', $i));
            $model = self::nonEmptyString($row['model'] ?? null, sprintf('prices[%d].model', $i));
            $validFrom = self::dateTime($row['valid_from'] ?? null, sprintf('prices[%d].valid_from', $i));
            $currency = self::nonEmptyString($row['currency'] ?? null, sprintf('prices[%d].currency', $i));
            $unit = self::nonEmptyString($row['unit'] ?? null, sprintf('prices[%d].unit', $i));
            $rates = self::rates($row['rates'] ?? null, $i, $unit);

            if ($unit !== self::UNIT_PER_1M && $unit !== self::UNIT_UNIFIED) {
                throw new InputFormatException(
                    sprintf('prices[%d].unit must be «%s» or «%s».', $i, self::UNIT_PER_1M, self::UNIT_UNIFIED),
                );
            }

            $out[] = [
                'provider' => $provider,
                'model' => $model,
                'valid_from' => $validFrom,
                'currency' => $currency,
                'unit' => $unit,
                'rates' => $rates,
            ];
        }

        return $out;
    }

    /**
     * @param mixed $value
     * @return array<string,string>
     */
    private static function rates(mixed $value, int $i, string $unit): array
    {
        if (!\is_array($value)) {
            throw new InputFormatException(sprintf('prices[%d].rates must be an object.', $i));
        }

        $required = match ($unit) {
            self::UNIT_PER_1M => self::PER_1M_RATES,
            self::UNIT_UNIFIED => ['any'],
            default => throw new InputFormatException(
                sprintf('prices[%d].unit must be «%s» or «%s».', $i, self::UNIT_PER_1M, self::UNIT_UNIFIED),
            ),
        };

        $out = [];
        foreach ($required as $kind) {
            $out[$kind] = self::decimalString($value[$kind] ?? null, sprintf('prices[%d].rates.%s', $i, $kind));
        }

        return $out;
    }

    /** @param mixed $value */
    private static function decimalString(mixed $value, string $path): string
    {
        if (\is_int($value)) {
            $value = (string) $value;
        }
        if (!\is_string($value) || !preg_match(self::DECIMAL_RE, $value)) {
            throw new InputFormatException(
                sprintf('%s must be a decimal string (non-negative, up to 6 fractional digits), got %s.',
                    $path, json_encode($value, JSON_UNESCAPED_UNICODE)),
            );
        }

        return $value;
    }

    /** @param mixed $value */
    private static function nonEmptyString(mixed $value, string $path): string
    {
        if (!\is_string($value) || $value === '') {
            throw new InputFormatException(
                sprintf('%s must be a non-empty string, got %s.',
                    $path, json_encode($value, JSON_UNESCAPED_UNICODE)),
            );
        }

        return $value;
    }

    /** @param array<mixed> $value */
    private static function assertAssoc(mixed $value, string $path): void
    {
        if (!\is_array($value) || array_is_list($value)) {
            throw new InputFormatException(sprintf('%s must be an object.', $path));
        }
    }

    /** @param mixed $value */
    private static function dateTime(mixed $value, string $path): DateTimeImmutable
    {
        if (!\is_string($value)) {
            throw new InputFormatException(sprintf('%s must be an ISO-8601 string.', $path));
        }

        $dt = DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $value)
            ?? DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $value);
        if ($dt === false) {
            throw new InputFormatException(sprintf('%s must be ISO-8601, got "%s".', $path, $value));
        }

        return $dt->setTimezone(new \DateTimeZone('UTC'));
    }

    /**
     * Разбирает и валидирует строку JSONL использования.
     *
     * @return array{
     *   provider:string, model:string, user:string,
     *   provider_request_id:string, ts:DateTimeImmutable,
     *   tokens_input:int, tokens_output:int, tokens_cache_read:int, tokens_cache_write:int,
     * }
     *
     * @throws InputFormatException
     */
    public static function usageRecord(string $json, int $lineNo): array
    {
        try {
            $row = json_decode($json, true, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InputFormatException(sprintf('line %d: invalid JSON — %s', $lineNo, $e->getMessage()), previous: $e);
        }

        if (!\is_array($row) || array_is_list($row)) {
            throw new InputFormatException(sprintf('line %d: must be a JSON object.', $lineNo));
        }

        $p = fn (string $field) => sprintf('line %d: %s', $lineNo, $field);

        $provider = self::nonEmptyString($row['provider'] ?? null, $p('provider must be a non-empty string'));
        $requestId = self::nonEmptyString($row['provider_request_id'] ?? null, $p('provider_request_id must be a non-empty string'));
        $model = self::nonEmptyString($row['model'] ?? null, $p('model must be a non-empty string'));
        $user = self::nonEmptyString($row['user'] ?? null, $p('user must be a non-empty string'));
        $ts = self::dateTime($row['ts'] ?? null, $p('ts must be an ISO-8601 UTC string'));

        $tokens = [
            'tokens_input' => self::tokens($row['tokens_input'] ?? 0, $p('tokens_input')),
            'tokens_output' => self::tokens($row['tokens_output'] ?? 0, $p('tokens_output')),
            'tokens_cache_read' => self::tokens($row['tokens_cache_read'] ?? 0, $p('tokens_cache_read')),
            'tokens_cache_write' => self::tokens($row['tokens_cache_write'] ?? 0, $p('tokens_cache_write')),
        ];

        return array_merge(
            [
                'provider' => $provider,
                'provider_request_id' => $requestId,
                'model' => $model,
                'user' => $user,
                'ts' => $ts,
            ],
            $tokens,
        );
    }

    private static function tokens(mixed $value, string $path): int
    {
        if (!\is_int($value) || $value < 0) {
            throw new InputFormatException(
                sprintf('%s must be an integer ≥ 0 (float запрещён), got %s.',
                    $path, json_encode($value, JSON_UNESCAPED_UNICODE)),
            );
        }

        return $value;
    }
}
