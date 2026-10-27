<?php

declare(strict_types=1);

namespace App\Domain;

use App\Domain\Catalog\CatalogFile;
use App\Domain\Catalog\ModelSpec;
use App\Domain\Catalog\PriceSpec;
use App\Domain\Catalog\ProviderSpec;
use App\Exception\ImportException;

/**
 * Разбор и валидация файла истории цен (JSON).
 *
 * Жёсткие правила:
 * - топы-уровень — объект; секции providers/models/prices — массивы;
 * - ставки — строки (не числа JSON), неотрицательное десятичное, до 6 знаков
 *   после точки; набор ключей — ровно тот, что требует схема (unit);
 * - off_peak.window_* — «HH:MM»; multiplier — десятичная строка > 0;
 * - valid_from — ISO-8601 UTC с суффиксом Z;
 * - unit — per_1m_tokens | per_1m_tokens_unified.
 *
 * Ссылки «провайдер/модель» проверяются сервисом импорта относительно
 * файла и уже загруженного справочника.
 */
final class CatalogParser
{
    private const ISO_UTC_TS = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(?:\.\d{1,6})?Z$/';
    private const DECIMAL = '/^\d{1,18}(?:\.\d{1,6})?$/';

    public function parse(string $rawJson): CatalogFile
    {
        try {
            $data = json_decode($rawJson, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException('Некорректный JSON: ' . $e->getMessage());
        }

        // пустой объект {} в PHP превращается в [], поэтому проверяем
        // «объектность» по неассоциативному разбору
        if (!is_object(json_decode($rawJson, false, 512, JSON_THROW_ON_ERROR))) {
            throw new ImportException('Корень файла должен быть JSON-объектом');
        }

        $providers = $this->parseSection('providers', $data, $this->parseProvider(...));
        $models = $this->parseSection('models', $data, $this->parseModel(...));
        $prices = $this->parseSection('prices', $data, $this->parsePrice(...));

        return new CatalogFile($providers, $models, $prices);
    }

    /**
     * @param array<string, mixed> $data
     * @param callable(array, int): T $parseItem
     *
     * @return list<T>
     *
     * @template T
     */
    private function parseSection(string $name, array $data, callable $parseItem): array
    {
        if (!array_key_exists($name, $data) || $data[$name] === null) {
            return [];
        }

        $items = $data[$name];
        if (!is_array($items) || !array_is_list($items)) {
            throw new ImportException("Секция \"{$name}\" должна быть массивом");
        }

        return array_values(array_map(
            static fn (int $no) => $parseItem($items[$no], $no),
            array_keys($items),
        ));
    }

    /**
     * @param mixed $item
     */
    private function parseProvider($item, int $no): ProviderSpec
    {
        $ctx = "providers[{$no}]";
        if (!is_array($item) || array_is_list($item)) {
            throw new ImportException("{$ctx}: элемент должен быть объектом");
        }

        $code = self::nonEmptyString($item['code'] ?? null, $ctx . '.code');
        $name = self::nonEmptyString($item['name'] ?? null, $ctx . '.name');

        if (array_key_exists('off_peak', $item) && $item['off_peak'] !== null) {
            $offPeak = self::parseOffPeak($item['off_peak'], $ctx . '.off_peak');
        } else {
            $offPeak = null;
        }

        return new ProviderSpec($code, $name, $offPeak);
    }

    /**
     * @param mixed $item
     */
    private function parseModel($item, int $no): ModelSpec
    {
        $ctx = "models[{$no}]";
        if (!is_array($item) || array_is_list($item)) {
            throw new ImportException("{$ctx}: элемент должен быть объектом");
        }

        return new ModelSpec(
            self::nonEmptyString($item['provider'] ?? null, $ctx . '.provider'),
            self::nonEmptyString($item['code'] ?? null, $ctx . '.code'),
        );
    }

    /**
     * @param mixed $item
     */
    private function parsePrice($item, int $no): PriceSpec
    {
        $ctx = "prices[{$no}]";
        if (!is_array($item) || array_is_list($item)) {
            throw new ImportException("{$ctx}: элемент должен быть объектом");
        }

        $provider = self::nonEmptyString($item['provider'] ?? null, $ctx . '.provider');
        $model = self::nonEmptyString($item['model'] ?? null, $ctx . '.model');
        $validFrom = self::parseDateTime($item['valid_from'] ?? null, $ctx . '.valid_from');
        $currency = self::nonEmptyString($item['currency'] ?? null, $ctx . '.currency');

        $unitRaw = $item['unit'] ?? null;
        $unit = \is_string($unitRaw) ? PriceUnit::tryFrom($unitRaw) : null;
        if ($unit === null) {
            throw new ImportException("{$ctx}.unit: должна быть per_1m_tokens или per_1m_tokens_unified");
        }

        $rates = self::parseRates($item['rates'] ?? null, $unit, $ctx);

        return new PriceSpec($provider, $model, $validFrom, $currency, $unit, $rates);
    }

    /**
     * @param mixed $value
     *
     * @return array{start: string, end: string, multiplier: string}
     */
    private static function parseOffPeak($value, string $ctx): array
    {
        if (!is_array($value) || array_is_list($value)) {
            throw new ImportException("{$ctx}: должно быть объектом с window_start, window_end, multiplier");
        }

        $start = self::nonEmptyString($value['window_start'] ?? null, $ctx . '.window_start');
        $end = self::nonEmptyString($value['window_end'] ?? null, $ctx . '.window_end');
        if (!OffPeakWindow::isValidHHMM($start) || !OffPeakWindow::isValidHHMM($end)) {
            throw new ImportException("{$ctx}: window_start/window_end должны быть в формате HH:MM");
        }

        $multiplier = self::nonEmptyString($value['multiplier'] ?? null, $ctx . '.multiplier');
        if (preg_match('/^\d{1,18}(?:\.\d+)?$/', $multiplier) !== 1 || bccomp($multiplier, '0', 30) <= 0) {
            throw new ImportException("{$ctx}.multiplier: должен быть десятичной строкой > 0");
        }

        return ['start' => $start, 'end' => $end, 'multiplier' => $multiplier];
    }

    /**
     * @param mixed $value
     */
    private static function parseDateTime($value, string $ctx): \DateTimeImmutable
    {
        if (!\is_string($value) || preg_match(self::ISO_UTC_TS, $value) !== 1) {
            throw new ImportException("{$ctx}: ожидается ISO-8601 UTC с суффиксом Z (2026-08-01T00:00:00Z)");
        }

        try {
            return new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        } catch (\Exception $e) {
            throw new ImportException("{$ctx}: некорректная дата — " . $e->getMessage());
        }
    }

    /**
     * @param mixed $value
     *
     * @return array<string, string>
     */
    private static function parseRates($value, PriceUnit $unit, string $ctx): array
    {
        if (!is_array($value) || array_is_list($value)) {
            throw new ImportException("{$ctx}.rates: должен быть объектом со ставками по видам токенов");
        }

        $required = $unit->requiredRateKeys();
        $result = [];
        foreach ($required as $key) {
            $rate = $value[$key] ?? null;
            if (!is_string($rate) || preg_match(self::DECIMAL, $rate) !== 1) {
                throw new ImportException(
                    "{$ctx}.rates.{$key}: ожидается строка с неотрицательным десятичным (до 6 знаков после точки)"
                );
            }
            $result[$key] = $rate;
        }

        $unknown = array_diff(array_keys($value), $required);
        if ($unknown !== []) {
            throw new ImportException(
                "{$ctx}.rates: неизвестные ключи: " . implode(', ', array_map('strval', $unknown))
            );
        }

        return $result;
    }

    /**
     * @param mixed $value
     */
    private static function nonEmptyString($value, string $ctx): string
    {
        if (!is_string($value) || $value === '') {
            throw new ImportException("{$ctx}: ожидается непустая строка");
        }

        return $value;
    }
}
