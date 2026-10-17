<?php

declare(strict_types=1);

namespace App\Application;

use App\Application\Dto\ModelData;
use App\Application\Dto\PriceData;
use App\Application\Dto\PricesFileData;
use App\Application\Dto\ProviderData;
use App\Domain\Billing\Iso8601;
use App\Domain\Billing\OffPeakPolicy;
use App\Domain\Exception\BillingException;
use App\Entity\Price;

/**
 * Разбор и валидация файла цен (JSON) в формате задания:
 * секции providers / models / prices.
 *
 * Правила:
 *  - секции отсутствуют — считаются пустыми (формат общего с файлом коррекции);
 *  - ставки/множители — только десятичные строки (числа JSON запрещены:
 *    float может потерять точность ещё до нашего чтения);
 *  - набор ключей rates строго соответствует схеме:
 *      per_1m_tokens        -> input, output, cache_read, cache_write;
 *      per_1m_tokens_unified -> any;
 *  - off_peak: окно проверяется как OffPeakPolicy (HH:MM, множитель).
 */
final class PricesFileParser
{
    private const DECIMAL_PATTERN = '/^\d{1,30}(?:\.\d{1,30})?$/';

    /** @var array<string, list<string>> ключи ставок каждой схемы */
    private const RATES_BY_UNIT = [
        Price::UNIT_PER_TOKEN => ['input', 'output', 'cache_read', 'cache_write'],
        Price::UNIT_UNIFIED => ['any'],
    ];

    public function parseFile(string $file): PricesFileData
    {
        if (!is_file($file) || !is_readable($file)) {
            throw new BillingException(sprintf('Файл не найден или недоступен для чтения: %s', $file));
        }

        $raw = file_get_contents($file);
        if ($raw === false) {
            throw new BillingException(sprintf('Не удалось прочитать файл: %s', $file));
        }

        return $this->parse($raw);
    }

    public function parse(string $raw): PricesFileData
    {
        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $exception) {
            throw new BillingException('Файл не является корректным JSON: '.$exception->getMessage(), 0, $exception);
        }

        if (!\is_array($data) || array_is_list($data)) {
            throw new BillingException('Корневой элемент файла со справочником цен должен быть JSON-объектом');
        }

        return new PricesFileData(
            providers: $this->parseProviders(self::section($data, 'providers')),
            models: $this->parseModels(self::section($data, 'models')),
            prices: $this->parsePrices(self::section($data, 'prices')),
        );
    }

    /**
     * @param array<int, mixed> $items
     *
     * @return list<ProviderData>
     */
    private function parseProviders(array $items): array
    {
        $result = [];

        foreach ($items as $index => $item) {
            $context = sprintf('провайдер #%d', $index);
            self::assertObject($item, $context);

            $code = self::nonEmptyString($item, 'code', $context);
            $name = $item['name'] ?? $code;
            if (!\is_string($name) || $name === '') {
                throw new BillingException(sprintf('%s: name должен быть непустой строкой', $context));
            }

            $offPeak = $item['off_peak'] ?? null;
            if ($offPeak === null) {
                $result[] = new ProviderData($code, $name, null, null, null);
                continue;
            }
            if (!\is_array($offPeak) || array_is_list($offPeak)) {
                throw new BillingException(sprintf('%s: off_peak должен быть JSON-объектом или null', $context));
            }

            $windowStart = self::nonEmptyString($offPeak, 'window_start', $context);
            $windowEnd = self::nonEmptyString($offPeak, 'window_end', $context);
            $multiplier = self::nonEmptyString($offPeak, 'multiplier', $context);

            // Конструктор проверяет HH:MM и десятичный множитель.
            $window = new OffPeakPolicy($windowStart, $windowEnd, $multiplier);
            $result[] = new ProviderData($code, $name, $window->windowStart(), $window->windowEnd(), $window->multiplier());
        }

        return $result;
    }

    /**
     * @param array<int, mixed> $items
     *
     * @return list<ModelData>
     */
    private function parseModels(array $items): array
    {
        $result = [];

        foreach ($items as $index => $item) {
            $context = sprintf('модель #%d', $index);
            self::assertObject($item, $context);

            $result[] = new ModelData(
                provider: self::nonEmptyString($item, 'provider', $context),
                code: self::nonEmptyString($item, 'code', $context),
            );
        }

        return $result;
    }

    /**
     * @param array<int, mixed> $items
     *
     * @return list<PriceData>
     */
    private function parsePrices(array $items): array
    {
        $result = [];

        foreach ($items as $index => $item) {
            $context = sprintf('строка цен #%d', $index);
            self::assertObject($item, $context);

            $provider = self::nonEmptyString($item, 'provider', $context);
            $model = self::nonEmptyString($item, 'model', $context);
            $validFromRaw = self::nonEmptyString($item, 'valid_from', $context);
            $validFrom = Iso8601::parseUtc($validFromRaw);

            $currency = $item['currency'] ?? 'USD';
            if (!\is_string($currency) || $currency === '') {
                throw new BillingException(sprintf('%s: currency должен быть непустой строкой', $context));
            }

            $unit = self::nonEmptyString($item, 'unit', $context);
            if (!isset(self::RATES_BY_UNIT[$unit])) {
                throw new BillingException(sprintf(
                    '%s: неизвестная схема тарификации "%s" (допустимые: %s)',
                    $context,
                    $unit,
                    implode(', ', array_keys(self::RATES_BY_UNIT)),
                ));
            }
            $expectedKinds = self::RATES_BY_UNIT[$unit];

            $rawRates = $item['rates'] ?? null;
            if (!\is_array($rawRates) || array_is_list($rawRates)) {
                throw new BillingException(sprintf('%s: rates должен быть JSON-объектом', $context));
            }
            foreach ($expectedKinds as $kind) {
                if (!\array_key_exists($kind, $rawRates)) {
                    throw new BillingException(sprintf('%s: отсутствует ставка "%s" (схема %s)', $context, $kind, $unit));
                }
            }
            $unexpected = array_values(array_diff(array_keys($rawRates), $expectedKinds));
            if ($unexpected !== []) {
                throw new BillingException(sprintf(
                    '%s: лишний ключи ставки %s для схемы %s',
                    $context,
                    implode(', ', array_map('strval', $unexpected)),
                    $unit,
                ));
            }

            $rates = [];
            foreach ($expectedKinds as $kind) {
                $value = $rawRates[$kind];
                if (!\is_string($value) || preg_match(self::DECIMAL_PATTERN, $value) !== 1) {
                    throw new BillingException(sprintf(
                        "%s: ставка '%s' должна быть строкой с неотрицательным десятичным числом",
                        $context,
                        $kind,
                    ));
                }
                $rates[$kind] = $value;
            }

            $result[] = new PriceData($provider, $model, $validFrom, $currency, $unit, $rates);
        }

        return $result;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return list<mixed>
     */
    private static function section(array $data, string $key): array
    {
        $value = $data[$key] ?? [];
        if (!\is_array($value) || array_is_list($value) === false) {
            throw new BillingException(sprintf("Секция '%s' должна быть JSON-массивом", $key));
        }

        return array_values($value);
    }

    /**
     * @param mixed             $item
     * @param array<string, mixed> $item
     */
    private static function assertObject(mixed $item, string $context): void
    {
        if (!\is_array($item) || array_is_list($item)) {
            throw new BillingException(sprintf('%s: должен быть JSON-объектом', $context));
        }
    }

    /**
     * @param array<string, mixed> $item
     */
    private static function nonEmptyString(array $item, string $key, string $context): string
    {
        $value = $item[$key] ?? null;
        if (!\is_string($value) || trim($value) === '') {
            throw new BillingException(sprintf('%s: поле "%s" должно быть непустой строкой', $context, $key));
        }

        return $value;
    }
}
