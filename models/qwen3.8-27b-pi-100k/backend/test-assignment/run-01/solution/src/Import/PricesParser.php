<?php

declare(strict_types=1);

namespace App\Import;

use App\Domain\CostCalculator;
use App\Domain\IsoTime;
use stdClass;

/**
 * Разбор и валидация JSON-файла со справочником цен.
 *
 * Режимы:
 *  - parseImport() — полный файл: секции providers, models, prices обязательны;
 *  - parseReprice() — файл коррекции: секции providers/models могут отсутствовать,
 *    секция prices обязательна и непустая.
 */
final class PricesParser extends AbstractJsonParser
{
    public function parseImport(string $json): PricesPayload
    {
        $root = $this->decodeRoot($json);

        return $this->parseRoot($root, importMode: true);
    }

    public function parseReprice(string $json): PricesPayload
    {
        $root = $this->decodeRoot($json);

        $payload = $this->parseRoot($root, importMode: false);

        if ($payload->priceRows === []) {
            throw new ImportException('Файл корректировки должен содержать непустую секцию "prices".');
        }

        return $payload;
    }

    private function decodeRoot(string $json): stdClass
    {
        try {
            $decoded = json_decode($json, false, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException('Файл не является корректным JSON: ' . $e->getMessage());
        }

        if (!($decoded instanceof \stdClass)) {
            throw new ImportException('Корнем файла должен быть объект JSON с секциями "providers", "models" и "prices".');
        }

        return $decoded;
    }

    /** @param stdClass $root */
    private function parseRoot(object $root, bool $importMode): PricesPayload
    {
        $providers = [];
        foreach ($this->section($root, 'providers', $importMode) as $i => $row) {
            $providers[] = $this->parseProvider($row, sprintf('providers[%d]', $i), $importMode);
        }

        $models = [];
        foreach ($this->section($root, 'models', $importMode) as $i => $row) {
            $models[] = $this->parseModel($row, sprintf('models[%d]', $i));
        }

        $priceRows = [];
        // Секция prices обязательна и в файле корректировки.
        foreach ($this->section($root, 'prices', true) as $i => $row) {
            $priceRows[] = $this->parsePriceRow($row, sprintf('prices[%d]', $i));
        }
        return new PricesPayload($providers, $models, $priceRows);
    }

    /** @return list<object> */
    private function section(object $root, string $name, bool $required): array
    {
        if (!\array_key_exists($name, get_object_vars($root))) {
            if (!$required) {
                return [];
            }

            throw new ImportException(sprintf('Отсутствует обязательная секция "%s".', $name));
        }

        $value = $root->{$name};
        if (!\is_array($value)) {
            throw new ImportException(sprintf('Секция "%s" должна быть списком объектов JSON.', $name));
        }

        foreach ($value as $i => $row) {
            if (!is_object($row)) {
                throw new ImportException(sprintf('Секция "%s": элемент %d должен быть объектом JSON.', $name, $i));
            }
        }

        return $value;
    }

    /**
     * @param stdClass $row
     * @param bool $importMode в режиме импорта отсутствующее off_peak означает «нет оф-пика»;
     *                         в режиме корректировки — «не менять сохранённое значение»
     */
    private function parseProvider(object $row, string $ctx, bool $importMode): ProviderSpec
    {
        $code = $this->string($row, 'code', $ctx);
        $name = $this->string($row, 'name', $ctx);

        $touched = \array_key_exists('off_peak', get_object_vars($row)) || $importMode;
        $offPeak = $row->off_peak ?? null;
        if ($offPeak === null) {
            return new ProviderSpec($code, $name, null, null, null, $touched);
        }
        if (!is_object($offPeak)) {
            throw new ImportException(sprintf('%s: off_peak должен быть объектом или null.', $ctx));
        }

        $windowStart = $this->clockToMinutes($this->string($offPeak, 'window_start', $ctx . '.off_peak'), $ctx . '.off_peak');
        $windowEnd = $this->clockToMinutes($this->string($offPeak, 'window_end', $ctx . '.off_peak'), $ctx . '.off_peak');
        $multiplier = $this->decimalString($offPeak, 'multiplier', $ctx . '.off_peak');

        return new ProviderSpec($code, $name, $windowStart, $windowEnd, $multiplier, true);
    }

    /** @param stdClass $row */
    private function parseModel(object $row, string $ctx): ModelSpec
    {
        return new ModelSpec(
            $this->string($row, 'provider', $ctx),
            $this->string($row, 'code', $ctx),
        );
    }

    /** @param stdClass $row */
    private function parsePriceRow(object $row, string $ctx): PriceSpec
    {
        $provider = $this->string($row, 'provider', $ctx);
        $model = $this->string($row, 'model', $ctx);

        $validFromRaw = $this->string($row, 'valid_from', $ctx);
        try {
            $validFrom = IsoTime::parseUtc($validFromRaw);
        } catch (\ValueError $e) {
            throw new ImportException(sprintf('%s: valid_from — %s', $ctx, $e->getMessage()));
        }

        $currency = $this->string($row, 'currency', $ctx);
        $unit = $this->string($row, 'unit', $ctx);
        $expectedKeys = match ($unit) {
            CostCalculator::UNIT_PER_KIND => ['input', 'output', 'cache_read', 'cache_write'],
            CostCalculator::UNIT_UNIFIED => ['any'],
            default => null,
        };
        if ($expectedKeys === null) {
            throw new ImportException(sprintf('%s: unit должен быть "%s" или "%s", получено "%s".', $ctx, CostCalculator::UNIT_PER_KIND, CostCalculator::UNIT_UNIFIED, $unit));
        }

        $rates = $row->rates ?? null;
        if (!is_object($rates)) {
            throw new ImportException(sprintf('%s: rates должен быть объектом JSON с десятичными строками.', $ctx));
        }

        $ratesMap = [];
        foreach (get_object_vars($rates) as $key => $value) {
            if (!\in_array($key, $expectedKeys, true)) {
                throw new ImportException(sprintf('%s: для схемы %s неизвестный ключ ставок "%s".', $ctx, $unit, $key));
            }
            $ratesMap[$key] = $this->decimalString((object) ['v' => $value], 'v', sprintf('%s.rates[%s]', $ctx, $key));
        }

        foreach ($expectedKeys as $key) {
            if (!\array_key_exists($key, $ratesMap)) {
                throw new ImportException(sprintf('%s: для схемы %s отсутствует ставка вида "%s".', $ctx, $unit, $key));
            }
        }

        return new PriceSpec($provider, $model, $validFrom, $currency, $unit, $ratesMap);
    }
}
