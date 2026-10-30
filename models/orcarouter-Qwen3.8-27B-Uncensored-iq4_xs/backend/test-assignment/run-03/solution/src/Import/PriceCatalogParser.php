<?php

declare(strict_types=1);

namespace App\Import;

use App\Billing\Money;
use App\Domain\Model\PriceLine;

/**
 * Парсинг и валидация файла истории цен (JSON).
 */
final class PriceCatalogParser
{
    /**
     * @return array{
     *     providers: array<array{code:string,name:string,offPeakStart:?string,offPeakEnd:?string,offPeakMultiplier:?string}>,
     *     models: array<array{provider:string,code:string}>,
     *     prices: array<array{provider:string,model:string,validFrom:\DateTimeImmutable,currency:string,unit:string,ratesJson:string}>
     * }
     *
     * @throws ImportException
     */
    public static function parseFile(string $path): array
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new ImportException('Файл не найден или нечитаем: '.$path);
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new ImportException('Не удалось прочитать файл: '.$path);
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            throw new ImportException('Некорректный JSON в файле: '.$path);
        }

        return self::parseData($data);
    }

    /**
     * @param array<mixed> $data
     * @return array{
     *     providers: array<int, array{code:string,name:string,offPeakStart:?string,offPeakEnd:?string,offPeakMultiplier:?string}>,
     *     models: array<int, array{provider:string,code:string}>,
     *     prices: array<int, array{provider:string,model:string,validFrom:\DateTimeImmutable,currency:string,unit:string,ratesJson:string}>
     * }
     *
     * @throws ImportException
     */
    public static function parseData(array $data): array
    {
        $providers = [];
        if (isset($data['providers']) && is_array($data['providers'])) {
            foreach ($data['providers'] as $i => $p) {
                if (!is_array($p)
                    || !self::isNonEmptyString($p['code'] ?? null)
                ) {
                    throw new ImportException('providers['.$i.']: отсутствует обязательное поле "code"');
                }

                $offPeakStart = null;
                $offPeakEnd = null;
                $offPeakMultiplier = null;
                $offPeak = $p['off_peak'] ?? null;
                if (is_array($offPeak)) {
                    if (!self::isHhMm($offPeak['window_start'] ?? null)
                        || !self::isHhMm($offPeak['window_end'] ?? null)
                        || !self::isDecimalString($offPeak['multiplier'] ?? null)
                    ) {
                        throw new ImportException('providers['.$p['code'].']: некорректный блок off_peak');
                    }
                    $offPeakStart = (string) $offPeak['window_start'];
                    $offPeakEnd = (string) $offPeak['window_end'];
                    $offPeakMultiplier = Money::clean((string) $offPeak['multiplier']);
                } elseif ($offPeak !== null) {
                    throw new ImportException('providers['.$p['code'].']: off_peak должен быть объектом или null');
                }

                $providers[] = [
                    'code' => (string) $p['code'],
                    'name' => self::isNonEmptyString($p['name'] ?? null) ? (string) $p['name'] : (string) $p['code'],
                    'offPeakStart' => $offPeakStart,
                    'offPeakEnd' => $offPeakEnd,
                    'offPeakMultiplier' => $offPeakMultiplier,
                ];
            }
        }

        $models = [];
        if (isset($data['models']) && is_array($data['models'])) {
            foreach ($data['models'] as $i => $m) {
                if (!is_array($m)
                    || !self::isNonEmptyString($m['provider'] ?? null)
                    || !self::isNonEmptyString($m['code'] ?? null)
                ) {
                    throw new ImportException('models['.$i.']: отсутствуют обязательные поля "provider"/"code"');
                }
                $models[] = [
                    'provider' => (string) $m['provider'],
                    'code' => (string) $m['code'],
                ];
            }
        }

        $prices = [];
        if (isset($data['prices']) && is_array($data['prices'])) {
            $prices = self::parsePrices($data['prices']);
        }

        return ['providers' => $providers, 'models' => $models, 'prices' => $prices];
    }

    /**
     * @param mixed $raw
     * @return array<int, array{provider:string,model:string,validFrom:\DateTimeImmutable,currency:string,unit:string,ratesJson:string}>
     *
     * @throws ImportException
     */
    private static function parsePrices($raw): array
    {
        if (!is_array($raw)) {
            throw new ImportException('секция "prices" должна быть массивом');
        }

        $out = [];
        foreach ($raw as $i => $p) {
            if (!is_array($p)
                || !self::isNonEmptyString($p['provider'] ?? null)
                || !self::isNonEmptyString($p['model'] ?? null)
            ) {
                throw new ImportException('prices['.$i.']: отсутствуют обязательные поля "provider"/"model"');
            }

            $validFrom = self::parseUtcDateTime($p['valid_from'] ?? null, 'prices['.$i.']');
            $unit = $p['unit'] ?? null;
            if (!is_string($unit) || !in_array($unit, [PriceLine::UNIT_TOKENS, PriceLine::UNIT_UNIFIED], true)) {
                throw new ImportException('prices['.$i.']: неизвестный unit: '.var_export($unit, true));
            }

            $currency = self::isNonEmptyString($p['currency'] ?? null) ? (string) $p['currency'] : 'USD';

            $rates = $p['rates'] ?? null;
            if (!is_array($rates) || $rates === []) {
                throw new ImportException('prices['.$i.']: секция "rates" отсутствует или пуста');
            }

            $ratesOut = [];
            foreach ($rates as $key => $value) {
                if (!is_string($value) && !is_int($value) && !is_float($value)) {
                    throw new ImportException('prices['.$i.']: ставка "'.$key.'" должна быть строкой');
                }
                $ratesOut[(string) $key] = Money::clean((string) $value);
            }

            if ($unit === PriceLine::UNIT_UNIFIED) {
                if (!isset($ratesOut['any'])) {
                    throw new ImportException('prices['.$i.']: для unified-схемы требуется ставка "any"');
                }
            } else {
                foreach (['input', 'output', 'cache_read', 'cache_write'] as $k) {
                    if (!isset($ratesOut[$k])) {
                        throw new ImportException('prices['.$i.']: для per-token схемы требуется ставка "'.$k.'"');
                    }
                }
            }

            $out[] = [
                'provider' => (string) $p['provider'],
                'model' => (string) $p['model'],
                'validFrom' => $validFrom,
                'currency' => $currency,
                'unit' => $unit,
                'ratesJson' => json_encode($ratesOut, JSON_THROW_ON_ERROR),
            ];
        }

        return $out;
    }

    /** @throws ImportException */
    private static function parseUtcDateTime(mixed $value, string $context): \DateTimeImmutable
    {
        if (!is_string($value)) {
            throw new ImportException($context.': поле valid_from должно быть строкой ISO-8601 (UTC)');
        }

        $dt = \DateTimeImmutable::createFromFormat('Y-m-d\TH:i:s\Z', $value, new \DateTimeZone('UTC'));
        if ($dt === false) {
            $dt = \DateTimeImmutable::createFromFormat(\DateTimeInterface::RFC3339, $value, new \DateTimeZone('UTC'));
        }

        if ($dt === false) {
            throw new ImportException($context.': не удалось разобрать время "'.$value.'"');
        }

        return $dt;
    }

    private static function isNonEmptyString(mixed $value): bool
    {
        return is_string($value) && $value !== '';
    }

    private static function isHhMm(mixed $value): bool
    {
        return is_string($value) && preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', $value) === 1;
    }

    private static function isDecimalString(mixed $value): bool
    {
        return is_string($value) && preg_match('/^-?\d+(\.\d+)?$/', $value) === 1;
    }
}
