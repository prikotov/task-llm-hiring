<?php

declare(strict_types=1);

namespace App\Service;

use App\DTO\PricesDocument;
use App\Domain\IsoUtc;
use App\Domain\RateCalculator;
use App\Exception\DataImportException;

/**
 * Парсинг и строгая валидация JSON-файла «история цен».
 *
 * Правила:
 *  - провайдеры: code, name, off_peak (null или объект {window_start,
 *    window_end, multiplier});
 *  - модели: provider, code (провайдер должен существовать — после
 *    upsert провайдеров из этого файла либо уже в базе);
 *  - строки цен: provider, model, valid_from (ISO-8601 UTC), currency
 *    (USD), unit (per_1m_tokens | per_1m_tokens_unified), rates — только
 *    только нужные схеме ставки; десятичные строки с ≤ 6 знаками после точки.
 */
final class PricesFileParser
{
    private const CODE = '/^[A-Za-z0-9][A-Za-z0-9_-]{0,63}$/';
    private const DECIMAL = '/^\d+(\.\d{1,6})?$/';
    private const WINDOW = '/^([01][0-9]|2[0-3]):[0-5][0-9]$/';

    public function parseFile(string $path): PricesDocument
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new DataImportException(sprintf('Файл не найден или не читается: "%s"', $path));
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new DataImportException(sprintf('Не удалось прочитать файл: "%s"', $path));
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            throw new DataImportException('Некорректный JSON: верхний уровень — объект');
        }

        $sections = ['providers', 'models', 'prices'];
        foreach ($sections as $section) {
            if (!array_key_exists($section, $data)) {
                // Секции разрешено опускать (файл коррекции); по умолчанию
                // секция считается пустой.
                $data[$section] = [];
            }
            if (!is_array($data[$section]) || !array_is_list($data[$section])) {
                throw new DataImportException(sprintf('Секция "%s" — массив объектов', $section));
            }
        }

        return new PricesDocument(
            $this->parseProviders($data['providers']),
            $this->parseModels($data['models']),
            $this->parsePrices($data['prices']),
        );
    }

    /**
     * @param array<int, mixed> $rows
     *
     * @return list<array{code: string, name: string, off_peak: array{window_start: string, window_end: string, multiplier: string}|null}>
     */
    private function parseProviders(array $rows): array
    {
        $result = [];
        foreach ($rows as $i => $row) {
            if (!is_array($row)) {
                throw new DataImportException(sprintf('providers[%d]: ожидается объект', $i));
            }

            $code = self::string($row, 'code', sprintf('providers[%d]', $i), 64);
            if (!preg_match(self::CODE, $code)) {
                throw new DataImportException(sprintf('providers[%d]: некорректный код "%s"', $i, $code));
            }

            $name = self::string($row, 'name', sprintf('providers[%d]', $i), 255);

            if (!array_key_exists('off_peak', $row)) {
                // Отсутствие поля допускаем как null (скидки нет).
                $offPeak = null;
            } else {
                $offPeak = $row['off_peak'];
                if ($offPeak === null) {
                    $offPeak = null;
                } elseif (is_array($offPeak)) {
                    $windowStart = self::string($offPeak, 'window_start', sprintf('providers[%d].off_peak', $i), 5);
                    $windowEnd = self::string($offPeak, 'window_end', sprintf('providers[%d].off_peak', $i), 5);
                    if (!preg_match(self::WINDOW, $windowStart) || !preg_match(self::WINDOW, $windowEnd)) {
                        throw new DataImportException(sprintf('providers[%d]: off_peak — ХХ:ММ по UTC', $i));
                    }

                    $multiplier = self::string($offPeak, 'multiplier', sprintf('providers[%d].off_peak', $i), 30);
                    if (!preg_match(self::DECIMAL, $multiplier)) {
                        throw new DataImportException(sprintf('providers[%d]: off_peak.multiplier — десятичная строка ≤ 6 знаков', $i));
                    }

                    $offPeak = [
                        'window_start' => $windowStart,
                        'window_end' => $windowEnd,
                        'multiplier' => $multiplier,
                    ];
                } else {
                    throw new DataImportException(sprintf('providers[%d]: off_peak — null или объект', $i));
                }
            }

            // Повтор code внутри одного файла — ошибка (иначе upsert затирал бы данные).
            if (in_array($code, array_column($result, 'code'), true)) {
                throw new DataImportException(sprintf('providers[%d]: дублируется код провайдера "%s"', $i, $code));
            }

            $result[] = [
                'code' => $code,
                'name' => $name,
                'off_peak' => $offPeak,
            ];
        }

        return $result;
    }

    /**
     * @param array<int, mixed> $rows
     *
     * @return list<array{provider: string, code: string}>
     */
    private function parseModels(array $rows): array
    {
        $result = [];
        foreach ($rows as $i => $row) {
            if (!is_array($row)) {
                throw new DataImportException(sprintf('models[%d]: ожидается объект', $i));
            }

            $provider = self::string($row, 'provider', sprintf('models[%d]', $i), 64);
            $code = self::string($row, 'code', sprintf('models[%d]', $i), 64);
            if (!preg_match(self::CODE, $code) || !preg_match(self::CODE, $provider)) {
                throw new DataImportException(sprintf('models[%d]: некорректный код модели или провайдера', $i));
            }

            $result[] = [
                'provider' => $provider,
                'code' => $code,
            ];
        }

        return $result;
    }

    /**
     * @param array<int, mixed> $rows
     *
     * @return list<array{provider: string, model: string, valid_from: \DateTimeImmutable, currency: string, unit: string, rates: array<string, string>}>
     */
    private function parsePrices(array $rows): array
    {
        $result = [];
        foreach ($rows as $i => $row) {
            if (!is_array($row)) {
                throw new DataImportException(sprintf('prices[%d]: ожидается объект', $i));
            }

            $at = sprintf('prices[%d]', $i);
            $provider = self::string($row, 'provider', $at, 64);
            $model = self::string($row, 'model', $at, 64);
            $validFromValue = self::string($row, 'valid_from', $at, 40);
            $currency = self::string($row, 'currency', $at, 3);
            if ($currency !== 'USD') {
                throw new DataImportException(sprintf('%s: поддерживается только валюта USD (фактически "%s")', $at, $currency));
            }

            $unit = self::string($row, 'unit', $at, 32);
            if (!in_array($unit, [RateCalculator::UNIT_PER_TOKEN, RateCalculator::UNIT_UNIFIED], true)) {
                throw new DataImportException(sprintf('%s: неизвестная схема тарификации "%s"', $at, $unit));
            }

            $result[] = [
                'provider' => $provider,
                'model' => $model,
                'valid_from' => IsoUtc::parse($validFromValue),
                'currency' => $currency,
                'unit' => $unit,
                'rates' => self::parseRates($row['rates'] ?? null, $unit, $at),
            ];
        }

        return $result;
    }

    /**
     * @return array<string, string>
     */
    private static function parseRates(mixed $rates, string $unit, string $at): array
    {
        if (!is_array($rates)) {
            throw new DataImportException(sprintf('%s: rates — объект со строками-числами', $at));
        }

        $required = $unit === RateCalculator::UNIT_UNIFIED
            ? ['any']
            : ['input', 'output', 'cache_read', 'cache_write'];

        foreach ($required as $key) {
            if (!array_key_exists($key, $rates)) {
                throw new DataImportException(sprintf('%s: отсутствует ставка "%s" для схемы %s', $at, $key, $unit));
            }
        }

        foreach ($rates as $key => $value) {
            if (!in_array($key, $required, true)) {
                throw new DataImportException(sprintf('%s: лишняя ставка "%s" для схемы %s', $at, (string) $key, $unit));
            }
            if (!is_string($value) || !preg_match(self::DECIMAL, $value)) {
                throw new DataImportException(sprintf('%s: ставка "%s" — десятичная строка ≤ 6 знаков', $at, (string) $key));
            }
        }

        return $rates;
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function string(array $row, string $field, string $at, int $maxLen): string
    {
        if (!array_key_exists($field, $row)) {
            throw new DataImportException(sprintf('%s: отсутствует поле "%s"', $at, $field));
        }

        $value = $row[$field];
        if (!is_string($value) || $value === '') {
            throw new DataImportException(sprintf('%s: поле "%s" — непустая строка', $at, $field));
        }

        if (mb_strlen($value) > $maxLen) {
            throw new DataImportException(sprintf('%s: поле "%s" — до %d символов', $at, $field, $maxLen));
        }

        return $value;
    }
}
