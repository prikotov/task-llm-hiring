<?php

declare(strict_types=1);

namespace App\Import;

use App\Domain\Iso8601;
use App\Domain\OffPeakWindow;
use App\Domain\PricingUnit;
use App\Exception\ImportException;

/**
 * Парсинг JSON-файла истории цен с валидацией.
 * Любое нарушение формата — ImportException (команда завершится кодом 1).
 */
final class PriceFileParser
{
    /**
     * @throws ImportException
     */
    public static function parseFile(string $path): PriceFile
    {
        if (!is_file($path)) {
            throw new ImportException('Файл не найден: ' . $path);
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new ImportException('Не удалось прочитать файл: ' . $path);
        }

        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException('Некорректный JSON в файле ' . $path . ': ' . $e->getMessage());
        }

        if (!is_array($data)) {
            throw new ImportException('Ожидался JSON-объект на верхнем уровне');
        }

        return self::parse($data);
    }

    /**
     * @param array<mixed> $data уже декодированный JSON-объект
     *
     * @throws ImportException
     */
    public static function parse(array $data): PriceFile
    {
        $providers = [];
        foreach (self::section($data, 'providers') as $i => $item) {
            self::assertObject($item, sprintf('providers[%d]', $i));
            $ctx = sprintf('providers[%d]', $i);
            $providers[] = new ProviderDefinition(
                self::string($item, 'code', $ctx),
                self::string($item, 'name', $ctx),
                self::offPeak($item['off_peak'] ?? null, $ctx),
            );
        }

        $models = [];
        foreach (self::section($data, 'models') as $i => $item) {
            self::assertObject($item, sprintf('models[%d]', $i));
            $ctx = sprintf('models[%d]', $i);
            $models[] = new ModelDefinition(
                self::string($item, 'provider', $ctx),
                self::string($item, 'code', $ctx),
            );
        }

        $prices = [];
        foreach (self::section($data, 'prices') as $i => $item) {
            self::assertObject($item, sprintf('prices[%d]', $i));
            $ctx = sprintf('prices[%d]', $i);
            try {
                $unit = PricingUnit::fromApiString(self::string($item, 'unit', $ctx));
                $prices[] = new PriceRowDefinition(
                    self::string($item, 'provider', $ctx),
                    self::string($item, 'model', $ctx),
                    self::dateTime($item, 'valid_from', $ctx),
                    self::string($item, 'currency', $ctx),
                    $unit,
                    self::rates($item['rates'] ?? null, $unit, $ctx),
                );
            } catch (\ValueError $e) {
                throw new ImportException($ctx . ': ' . $e->getMessage());
            }
        }

        return new PriceFile($providers, $models, $prices);
    }

    /**
     * @param array<mixed> $data
     *
     * @throws ImportException
     */
    private static function section(array $data, string $key): array
    {
        if (!array_key_exists($key, $data)) {
            return [];
        }

        $value = $data[$key];
        if (!array_is_list($value)) {
            throw new ImportException('Параметр «' . $key . '» должен быть массивом');
        }

        return $value;
    }

    /**
     * @throws ImportException
     */
    private static function assertObject(mixed $value, string $ctx): void
    {
        if (!is_array($value)) {
            throw new ImportException($ctx . ': ожидался объект');
        }
    }

    /**
     * @throws ImportException
     */
    private static function string(array $item, string $field, string $ctx): string
    {
        $value = $item[$field] ?? null;
        if (!is_string($value) || $value === '') {
            throw new ImportException($ctx . '.' . $field . ': ожидалась непустая строка');
        }

        return $value;
    }

    /**
     * @throws ImportException
     */
    private static function dateTime(array $item, string $field, string $ctx): \DateTimeImmutable
    {
        $value = $item[$field] ?? null;
        if (!is_string($value)) {
            throw new ImportException($ctx . '.' . $field . ': ожидался ISO-8601 timestamp (строка)');
        }

        try {
            return Iso8601::parseUtc($value);
        } catch (\ValueError $e) {
            throw new ImportException($ctx . '.' . $field . ': ' . $e->getMessage());
        }
    }

    /**
     * @throws ImportException
     */
    private static function offPeak(mixed $raw, string $ctx): ?OffPeakWindow
    {
        if ($raw === null) {
            return null;
        }

        if (!is_array($raw)) {
            throw new ImportException($ctx . '.off_peak: ожидался объект или null');
        }

        try {
            return OffPeakWindow::parse(
                self::string($raw, 'window_start', $ctx),
                self::string($raw, 'window_end', $ctx),
                self::decimal($raw, 'multiplier', $ctx),
            );
        } catch (\ValueError $e) {
            throw new ImportException($ctx . '.off_peak: ' . $e->getMessage());
        }
    }

    /**
     * @return array<string, string>
     *
     * @throws ImportException
     */
    private static function rates(mixed $raw, PricingUnit $unit, string $ctx): array
    {
        if (!is_array($raw)) {
            throw new ImportException($ctx . '.rates: ожидался объект со ставками');
        }

        $result = [];
        foreach ($unit->requiredRateKeys() as $key) {
            if (!array_key_exists($key, $raw)) {
                throw new ImportException(sprintf('%s.rates: не задана ставка "%s" для схемы %s', $ctx, $key, $unit->value));
            }

            $result[$key] = self::decimal($raw, $key, $ctx . '.rates');
        }

        return $result;
    }

    /**
     * @throws ImportException
     */
    private static function decimal(array $item, string $field, string $ctx): string
    {
        $value = $item[$field] ?? null;
        if (!is_string($value) || preg_match('/^\d+(\.\d+)?$/', $value) !== 1) {
            throw new ImportException($ctx . '.' . $field . ': ожидалась строка с неотрицательным десятичным числом');
        }

        return $value;
    }
}
