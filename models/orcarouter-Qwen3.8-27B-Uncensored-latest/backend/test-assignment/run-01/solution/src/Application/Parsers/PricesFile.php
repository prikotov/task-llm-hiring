<?php

declare(strict_types=1);

namespace App\Application\Parsers;

use App\Domain\Exception\ImportException;

/**
 * Разбор и валидация файла истории цен (JSON), в т.ч. файла коррекции цены
 * (формат тот же, секции providers/models в коррекции могут отсутствовать).
 *
 * Результат — нормализованные строки:
 *   providers: ['code' => string, 'name' => string,
 *               'offpeak' => null|['start','end','multiplier']]
 *   models:    ['provider' => string, 'code' => string]
 *   prices:    ['provider','model','valid_from'(DateTimeImmutable),
 *               'currency','unit','rates'(array<string,string>)]
 *
 * @return array{providers: list<array<string, mixed>>, models: list<array<string, string>>, prices: list<array<string, mixed>>}
 */
final class PricesFile
{
    private const CURRENCY = 'USD';
    private const UNITS = ['per_1m_tokens', 'per_1m_tokens_unified'];

    /** @return array{providers: list<array>, models: list<array>, prices: list<array>} */
    public static function parse(string $json, bool $requirePrices): array
    {
        $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);
        if (!is_array($decoded)) {
            throw ImportException::invalid('файл', 'корневой элемент JSON должен быть объектом');
        }

        return [
            'providers' => isset($decoded['providers']) ? self::providers($decoded['providers']) : [],
            'models' => isset($decoded['models']) ? self::models($decoded['models']) : [],
            'prices' => $requirePrices
                ? self::prices($decoded['prices'] ?? [])
                : (isset($decoded['prices']) ? self::prices($decoded['prices']) : []),
        ];
    }

    /** @param mixed $raw @return list<array<string, mixed>> */
    private static function providers(mixed $raw): array
    {
        if (!is_array($raw) || $raw === []) {
            return [];
        }
        if (array_is_list($raw) === false) {
            throw ImportException::invalid('providers', 'ожидается массив объектов');
        }

        $result = [];
        foreach ($raw as $i => $item) {
            $context = sprintf('providers[%d]', $i);
            if (!is_array($item)) {
                throw ImportException::invalid($context, 'ожидается объект');
            }

            $code = Scalar::nonEmptyString($item['code'] ?? null, 'code', $context);
            $name = Scalar::nonEmptyString($item['name'] ?? null, 'name', $context);
            $result[] = [
                'code' => $code,
                'name' => $name,
                'offpeak' => self::offPeak($item['off_peak'] ?? null, $context),
            ];
        }

        return $result;
    }

    /** @param list<array> $raw @return list<array<string, string>> */
    private static function models(array $raw): array
    {
        $result = [];
        foreach ($raw as $i => $item) {
            $context = sprintf('models[%d]', $i);
            if (!is_array($item)) {
                throw ImportException::invalid($context, 'ожидается объект');
            }
            $result[] = [
                'provider' => Scalar::nonEmptyString($item['provider'] ?? null, 'provider', $context),
                'code' => Scalar::nonEmptyString($item['code'] ?? null, 'code', $context),
            ];
        }

        return $result;
    }

    /** @param mixed $raw @return list<array<string, mixed>> */
    private static function prices(mixed $raw): array
    {
        if (!is_array($raw) || $raw === []) {
            throw ImportException::invalid('prices', 'ожидается непустой массив строк цен');
        }

        $result = [];
        foreach ($raw as $i => $item) {
            $context = sprintf('prices[%d]', $i);
            if (!is_array($item)) {
                throw ImportException::invalid($context, 'ожидается объект');
            }

            $unit = $item['unit'] ?? null;
            if (!is_string($unit) || !in_array($unit, self::UNITS, true)) {
                throw ImportException::invalid($context, 'unit должен быть "per_1m_tokens" или "per_1m_tokens_unified"');
            }

            $currency = $item['currency'] ?? null;
            if (!is_string($currency) || $currency !== self::CURRENCY) {
                throw ImportException::invalid($context, 'currency должен быть "USD"');
            }

            $validFrom = Scalar::isoUtc($item['valid_from'] ?? null, $context);

            $result[] = [
                'provider' => Scalar::nonEmptyString($item['provider'] ?? null, 'provider', $context),
                'model' => Scalar::nonEmptyString($item['model'] ?? null, 'model', $context),
                'valid_from' => $validFrom,
                'currency' => $currency,
                'unit' => $unit,
                'rates' => self::rates($item['rates'] ?? null, $unit, $context),
            ];
        }

        return $result;
    }

    /** @return array<string,string> */
    private static function rates(mixed $raw, string $unit, string $context): array
    {
        if (!is_array($raw) || $raw === []) {
            throw ImportException::invalid($context.'.rates', 'ожидается объект с названиями тарифов');
        }

        $required = match ($unit) {
            'per_1m_tokens' => ['input', 'output', 'cache_read', 'cache_write'],
            default => ['any'],
        };

        foreach ($raw as $key => $value) {
            if (!is_string($key) || !in_array($key, $required, true)) {
                throw ImportException::invalid(
                    $context.'.rates',
                    sprintf('недопустимый ключ тарифа "%s" для схемы %s (допустимо: %s)', $key, $unit, implode(', ', $required)),
                );
            }
        }
        foreach ($required as $key) {
            if (!array_key_exists($key, $raw)) {
                throw ImportException::invalid(
                    $context.'.rates',
                    sprintf('нет тарифа %s для схемы %s', $key, $unit),
                );
            }
        }

        $rates = [];
        foreach ($required as $key) {
            $rates[$key] = Scalar::decimal(is_string($raw[$key]) ? $raw[$key] : '', $context.'.rates.'.$key);
        }

        return $rates;
    }

    /** @return null|array{start:string,end:string,multiplier:string} */
    private static function offPeak(mixed $raw, string $context): ?array
    {
        if ($raw === null) {
            return null;
        }
        if (!is_array($raw)) {
            throw ImportException::invalid($context.'.off_peak', 'ожидается null или объект');
        }

        return [
            'start' => Scalar::timeOfDay(Scalar::nonEmptyString($raw['window_start'] ?? null, 'window_start', $context.'.off_peak'), $context.'.off_peak'),
            'end' => Scalar::timeOfDay(Scalar::nonEmptyString($raw['window_end'] ?? null, 'window_end', $context.'.off_peak'), $context.'.off_peak'),
            'multiplier' => Scalar::decimal(
                is_string($raw['multiplier'] ?? null) ? $raw['multiplier'] : '',
                $context.'.off_peak.multiplier',
            ),
        ];
    }
}
