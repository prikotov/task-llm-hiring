<?php

declare(strict_types=1);

namespace App\Infrastructure\Json;

use InvalidArgumentException;
use RuntimeException;

/**
 * Читает и валидирует файл истории цен. Любая структурная проблема —
 * исключение: частично применять такой файл нельзя.
 */
final class PriceListReader
{
    public function read(string $path): PriceListData
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new RuntimeException(sprintf('Файл не найден или недоступен для чтения: %s', $path));
        }

        $raw = file_get_contents($path);
        if (false === $raw) {
            throw new RuntimeException(sprintf('Не удалось прочитать файл: %s', $path));
        }

        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InvalidArgumentException(sprintf('Некорректный JSON в файле %s: %s', $path, $e->getMessage()), 0, $e);
        }

        if (!is_array($decoded)) {
            throw new InvalidArgumentException('Ожидался JSON-объект верхнего уровня.');
        }

        return new PriceListData(
            $this->readProviders($decoded['providers'] ?? []),
            $this->readModels($decoded['models'] ?? []),
            $this->readPrices($decoded['prices'] ?? []),
        );
    }

    /**
     * @return list<array{code: string, name: string, off_peak: array{window_start: string, window_end: string, multiplier: string}|null}>
     */
    private function readProviders(mixed $providers): array
    {
        $result = [];
        foreach ($this->listOf($providers, 'providers') as $index => $provider) {
            $provider = $this->objectOf($provider, sprintf('providers[%d]', $index));
            $offPeak = $provider['off_peak'] ?? null;

            if (null !== $offPeak) {
                $offPeak = $this->objectOf($offPeak, sprintf('providers[%d].off_peak', $index));
                $offPeak = [
                    'window_start' => $this->stringOf($offPeak['window_start'] ?? null, sprintf('providers[%d].off_peak.window_start', $index)),
                    'window_end' => $this->stringOf($offPeak['window_end'] ?? null, sprintf('providers[%d].off_peak.window_end', $index)),
                    'multiplier' => $this->stringOf($offPeak['multiplier'] ?? null, sprintf('providers[%d].off_peak.multiplier', $index)),
                ];
            }

            $result[] = [
                'code' => $this->stringOf($provider['code'] ?? null, sprintf('providers[%d].code', $index)),
                'name' => $this->stringOf($provider['name'] ?? null, sprintf('providers[%d].name', $index)),
                'off_peak' => $offPeak,
            ];
        }

        return $result;
    }

    /**
     * @return list<array{provider: string, code: string}>
     */
    private function readModels(mixed $models): array
    {
        $result = [];
        foreach ($this->listOf($models, 'models') as $index => $model) {
            $model = $this->objectOf($model, sprintf('models[%d]', $index));
            $result[] = [
                'provider' => $this->stringOf($model['provider'] ?? null, sprintf('models[%d].provider', $index)),
                'code' => $this->stringOf($model['code'] ?? null, sprintf('models[%d].code', $index)),
            ];
        }

        return $result;
    }

    /**
     * @return list<array{provider: string, model: string, valid_from: string, currency: string, unit: string, rates: array<string, string>}>
     */
    private function readPrices(mixed $prices): array
    {
        $result = [];
        foreach ($this->listOf($prices, 'prices') as $index => $price) {
            $price = $this->objectOf($price, sprintf('prices[%d]', $index));
            $rates = $this->objectOf($price['rates'] ?? null, sprintf('prices[%d].rates', $index));

            $normalizedRates = [];
            foreach ($rates as $key => $value) {
                $normalizedRates[(string) $key] = $this->stringOf($value, sprintf('prices[%d].rates.%s', $index, $key));
            }

            $result[] = [
                'provider' => $this->stringOf($price['provider'] ?? null, sprintf('prices[%d].provider', $index)),
                'model' => $this->stringOf($price['model'] ?? null, sprintf('prices[%d].model', $index)),
                'valid_from' => $this->stringOf($price['valid_from'] ?? null, sprintf('prices[%d].valid_from', $index)),
                'currency' => $this->stringOf($price['currency'] ?? null, sprintf('prices[%d].currency', $index)),
                'unit' => $this->stringOf($price['unit'] ?? null, sprintf('prices[%d].unit', $index)),
                'rates' => $normalizedRates,
            ];
        }

        return $result;
    }

    /**
     * @return list<mixed>
     */
    private function listOf(mixed $value, string $field): array
    {
        if (null === $value) {
            return [];
        }
        if (!is_array($value) || !array_is_list($value)) {
            throw new InvalidArgumentException(sprintf('Поле "%s" должно быть массивом.', $field));
        }

        return $value;
    }

    /**
     * @return array<string, mixed>
     */
    private function objectOf(mixed $value, string $field): array
    {
        if (!is_array($value) || array_is_list($value) && [] !== $value) {
            throw new InvalidArgumentException(sprintf('Поле "%s" должно быть объектом.', $field));
        }

        /** @var array<string, mixed> $value */
        return $value;
    }

    private function stringOf(mixed $value, string $field): string
    {
        if (!is_string($value) || '' === $value) {
            throw new InvalidArgumentException(sprintf('Поле "%s" должно быть непустой строкой.', $field));
        }

        return $value;
    }
}
