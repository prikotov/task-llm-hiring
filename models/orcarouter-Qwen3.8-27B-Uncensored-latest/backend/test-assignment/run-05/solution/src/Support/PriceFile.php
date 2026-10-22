<?php

declare(strict_types=1);

namespace App\Support;

use App\Domain\BillingUnit;

/**
 * Разбор и валидация файла истории цен (JSON) в нормализованное представление.
 *
 * Формат описан в TASK.md: секции providers / models / prices;
 * ставки — десятичные строки; временна́я метка valid_from — ISO-8601.
 */
final class PriceFile
{
    private const DECIMAL_RE = '/^(0|[1-9]\d*)(\.\d{1,12})?$/';
    private const WINDOW_RE = '/^([01]\d|2[0-3]):([0-5]\d)$/';
    private const DATE_RE = '/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}(\.\d+)?(Z|[+-]\d{2}:?\d{2})$/';

    /**
     * @return array{
     *     providers: list<array{code: string, name: string, off_peak: ?array{window_start: string, window_end: string, multiplier: string}}>,
     *     models: list<array{provider: string, code: string}>,
     *     prices: list<array{provider: string, model: string, valid_from: \DateTimeImmutable, currency: string, unit: BillingUnit, rates: array<string, string>}>,
     * }
     *
     * @throws ImportError
     */
    public static function parse(string $path): array
    {
        if (!is_file($path)) {
            throw new ImportError('Файл не найден: '.$path);
        }
        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new ImportError('Не удалось прочитать файл: '.$path);
        }

        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR | JSON_INVALID_UTF8_SUBSTITUTE);
        } catch (\JsonException $e) {
            throw new ImportError('Некорректный JSON: '.$e->getMessage());
        }
        if (!is_array($data)) {
            throw new ImportError('Корень JSON должен быть объектом');
        }

        $result = [
            'providers' => self::parseProviders(self::section($data, 'providers')),
            'models' => self::parseModels(self::section($data, 'models')),
            'prices' => self::parsePrices(self::section($data, 'prices')),
        ];

        return $result;
    }

    /** @return list<array<string, mixed>> */
    private static function section(array $data, string $name): array
    {
        if (!array_key_exists($name, $data)) {
            return [];
        }
        $value = $data[$name];
        if (!is_array($value)) {
            throw new ImportError(sprintf('Секция "%s" должна быть массивом', $name));
        }
        if (array_keys($value) !== range(0, count($value) - 1)) {
            throw new ImportError(sprintf('Секция "%s" должна быть массивом-списком', $name));
        }

        return $value;
    }

    /**
     * @param list<array<string, mixed>> $list
     * @return list<array{code: string, name: string, off_peak: ?array{window_start: string, window_end: string, multiplier: string}}>
     */
    private static function parseProviders(array $list): array
    {
        $out = [];
        foreach ($list as $i => $p) {
            $label = sprintf('providers[%d]', $i);
            if (!is_array($p)) {
                throw new ImportError($label.': ожидается объект');
            }
            $code = self::string($p, 'code', $label);
            $name = self::string($p, 'name', $label);

            $offPeak = null;
            if (array_key_exists('off_peak', $p)) {
                if ($p['off_peak'] === null) {
                    $offPeak = null;
                } elseif (is_array($p['off_peak'])) {
                    $label .= '.off_peak';
                    $offPeak = [
                        'window_start' => self::window($p['off_peak'], 'window_start', $label),
                        'window_end' => self::window($p['off_peak'], 'window_end', $label),
                        'multiplier' => self::multiplier($p['off_peak'], 'multiplier', $label),
                    ];
                } else {
                    throw new ImportError($label.': ожидается объект или null');
                }
            }

            $out[] = ['code' => $code, 'name' => $name, 'off_peak' => $offPeak];
        }

        return $out;
    }

    /**
     * @param list<array<string, mixed>> $list
     * @return list<array{provider: string, code: string}>
     */
    private static function parseModels(array $list): array
    {
        $out = [];
        foreach ($list as $i => $m) {
            $label = sprintf('models[%d]', $i);
            if (!is_array($m)) {
                throw new ImportError($label.': ожидается объект');
            }
            $out[] = [
                'provider' => self::string($m, 'provider', $label),
                'code' => self::string($m, 'code', $label),
            ];
        }

        return $out;
    }

    /**
     * @param list<array<string, mixed>> $list
     * @return list<array{provider: string, model: string, valid_from: \DateTimeImmutable, currency: string, unit: BillingUnit, rates: array<string, string>}>
     */
    private static function parsePrices(array $list): array
    {
        $out = [];
        foreach ($list as $i => $p) {
            $label = sprintf('prices[%d]', $i);
            if (!is_array($p)) {
                throw new ImportError($label.': ожидается объект');
            }

            $ratesRaw = $p['rates'] ?? null;
            if (!is_array($ratesRaw)) {
                throw new ImportError($label.": 'rates' должен быть объектом");
            }

            $unitRaw = self::string($p, 'unit', $label);
            $unit = BillingUnit::tryFrom($unitRaw);
            if ($unit === null) {
                throw new ImportError($label.": неизвестная схема тарификации '".$unitRaw."'");
            }
            $expected = $unit->rateKeys();
            $rates = [];
            foreach (array_keys($ratesRaw) as $key) {
                if (!in_array($key, $expected, true)) {
                    throw new ImportError($label.": неизвестная ставка '".$key."' для схемы '".$unitRaw."'");
                }
            }
            foreach ($expected as $key) {
                if (!array_key_exists($key, $ratesRaw)) {
                    throw new ImportError($label.": отсутствует требуемая ставка '".$key."'");
                }
                $rates[$key] = self::decimal($ratesRaw, $key, $label.'.rates');
            }

            $validFromRaw = self::string($p, 'valid_from', $label);
            if (!preg_match(self::DATE_RE, $validFromRaw)) {
                throw new ImportError($label.": valid_from '".$validFromRaw."' — ожидается ISO-8601");
            }
            try {
                $validFrom = new \DateTimeImmutable($validFromRaw, new \DateTimeZone('UTC'));
            } catch (\Exception $e) {
                throw new ImportError($label.": valid_from '".$validFromRaw."' — ошибка разбора: ".$e->getMessage());
            }

            $currency = $p['currency'] ?? 'USD';
            if (!is_string($currency) || $currency === '') {
                throw new ImportError($label.": поле 'currency' должно быть непустой строкой");
            }

            $out[] = [
                'provider' => self::string($p, 'provider', $label),
                'model' => self::string($p, 'model', $label),
                'valid_from' => $validFrom,
                'currency' => $currency,
                'unit' => $unit,
                'rates' => $rates,
            ];
        }

        return $out;
    }

    /** @param array<string, mixed> $obj */
    private static function string(array $obj, string $key, string $label): string
    {
        $value = $obj[$key] ?? null;
        if (!is_string($value) || $value === '') {
            throw new ImportError(sprintf('%s: поле "%s" должно быть непустой строкой', $label, $key));
        }

        return $value;
    }

    /** @param array<string, mixed> $obj */
    private static function window(array $obj, string $key, string $label): string
    {
        $value = self::string($obj, $key, $label);
        if (!preg_match(self::WINDOW_RE, $value)) {
            throw new ImportError(sprintf('%s: поле "%s" должно быть в формате HH:MM, получено "%s"', $label, $key, $value));
        }

        return $value;
    }

    /** @param array<string, mixed> $obj */
    private static function multiplier(array $obj, string $key, string $label): string
    {
        $value = self::decimal($obj, $key, $label);
        if (bccomp($value, '0', 6) === 0) {
            throw new ImportError(sprintf('%s: множитель должен быть > 0', $label));
        }

        return $value;
    }

    /** @param array<string, mixed> $obj */
    private static function decimal(array $obj, string $key, string $label): string
    {
        $value = $obj[$key] ?? null;
        if (!is_string($value) || !preg_match(self::DECIMAL_RE, $value)) {
            throw new ImportError(sprintf('%s: ставка "%s" должна быть неотрицательным десятичным числом строкой', $label, $key));
        }

        return $value;
    }
}
