<?php

declare(strict_types=1);

namespace App\Application\Catalog;

use App\Domain\OffPeakPolicy;
use App\Domain\UnitRates;
use App\Entity\PriceRow;
use App\Exception\ImportException;
use DateTimeImmutable;
use DateTimeZone;

/**
 * Валидация элементов справочника цен (провайдеры, модели, строки цен)
 * в типизированные структуры. Не знает о файлах и о БД.
 */
final class CatalogParser
{
    /** @return list<array{code: string, name: string, offPeak: ?OffPeakPolicy}> */
    public static function parseProviders(array $data): array
    {
        $result = [];
        foreach ($data as $i => $spec) {
            if (!is_array($spec)) {
                throw new ImportException(sprintf('providers[%d]: ожидается объект JSON', $i));
            }
            $where = sprintf('providers[%d]', $i);
            $code = self::string($spec, 'code', $where);
            $name = self::string($spec, 'name', $where);

            $offPeak = null;
            if (array_key_exists('off_peak', $spec) && $spec['off_peak'] !== null) {
                if (!is_array($spec['off_peak'])) {
                    throw new ImportException("$where.off_peak: ожидается объект или null");
                }
                $offPeak = OffPeakPolicy::fromSpec($spec['off_peak']);
            }

            $result[] = ['code' => $code, 'name' => $name, 'offPeak' => $offPeak];
        }

        return $result;
    }

    /** @return list<array{provider: string, code: string}> */
    public static function parseModels(array $data): array
    {
        $result = [];
        foreach ($data as $i => $spec) {
            if (!is_array($spec)) {
                throw new ImportException(sprintf('models[%d]: ожидается объект JSON', $i));
            }
            $where = sprintf('models[%d]', $i);
            $result[] = [
                'provider' => self::string($spec, 'provider', $where),
                'code' => self::string($spec, 'code', $where),
            ];
        }

        return $result;
    }

    /**
     * @return list<array{provider: string, model: string, validFrom: DateTimeImmutable,
     *                    currency: string, rates: UnitRates}>
     */
    public static function parsePrices(array $data): array
    {
        $result = [];
        foreach ($data as $i => $spec) {
            if (!is_array($spec)) {
                throw new ImportException(sprintf('prices[%d]: ожидается объект JSON', $i));
            }
            $where = sprintf('prices[%d]', $i);
            $unit = $spec['unit'] ?? null;
            $rates = self::parseRates($spec['rates'] ?? null, $unit, $where);

            $result[] = [
                'provider' => self::string($spec, 'provider', $where),
                'model' => self::string($spec, 'model', $where),
                'validFrom' => self::parseTimestamp($spec['valid_from'] ?? null, 'valid_from', $where),
                'currency' => isset($spec['currency']) ? self::string($spec, 'currency', $where) : 'USD',
                'rates' => $rates,
            ];
        }

        return $result;
    }

    /**
     * @param string|null $unit
     */
    private static function parseRates(array|null $rates, string|null $unit, string $where): UnitRates
    {
        if (!is_array($rates)) {
            throw new ImportException("$where.rates: ожидается объект JSON со ставками");
        }

        if ($unit === PriceRow::UNIT_PER_TOKEN) {
            return new UnitRates(
                unit: PriceRow::UNIT_PER_TOKEN,
                input: self::rate($rates, 'input'),
                output: self::rate($rates, 'output'),
                cacheRead: self::rate($rates, 'cache_read'),
                cacheWrite: self::rate($rates, 'cache_write'),
            );
        }

        if ($unit === PriceRow::UNIT_UNIFIED) {
            return new UnitRates(unit: PriceRow::UNIT_UNIFIED, any: self::rate($rates, 'any'));
        }

        $known = implode(', ', [PriceRow::UNIT_PER_TOKEN, PriceRow::UNIT_UNIFIED]);

        throw new ImportException("$where.unit: допустимые значения: $known; получено: " . var_export($unit, true));
    }

    private static function rate(array $rates, string $key): string
    {
        $value = $rates[$key] ?? null;
        if (!is_string($value) && !is_float($value) && !is_int($value)) {
            throw new ImportException(sprintf('rates.%s: ожидается десятичное число (в спецификации — строка)', $key));
        }
        $value = (string) $value;
        if (!preg_match('/^\d+(\.\d+)?$/', $value)) {
            throw new ImportException(sprintf('rates.%s: ожидается неотрицательное десятичное число, получено: %s', $key, $value));
        }
        $pos = strpos($value, '.');
        if ($pos !== false && strlen(substr($value, $pos + 1)) > 6) {
            throw new ImportException(sprintf('rates.%s: не более 6 знаков после точки (получено: %s)', $key, $value));
        }

        return $value;
    }

    private static function string(array $spec, string $field, string $where): string
    {
        $value = $spec[$field] ?? null;
        if (!is_string($value) || trim($value) === '') {
            throw new ImportException("$where.$field: ожидается непустая строка");
        }

        return $value;
    }

    public static function parseTimestamp(mixed $value, string $field, string $where): DateTimeImmutable
    {
        if (!is_string($value)) {
            throw new ImportException("$where.$field: ожидается ISO-8601 время в UTC");
        }
        try {
            $ts = new DateTimeImmutable($value, new DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new ImportException("$where.$field: неверный формат времени: $value");
        }
        if ($ts->getTimezone()->getOffset($ts) !== 0) {
            throw new ImportException("$where.$field: время должно быть UTC (Z), получено: $value");
        }

        return $ts->setTimezone(new DateTimeZone('UTC'));
    }
}
