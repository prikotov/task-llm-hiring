<?php

declare(strict_types=1);

namespace App\Application\Pricing;

use App\Application\Exception\FileFormatException;
use App\Domain\Common\Iso8601Utc;
use App\Domain\Pricing\OffPeakWindow;
use App\Domain\Pricing\PriceUnit;
use App\Domain\Pricing\TokenKind;
use DateTimeImmutable;
use Exception;
use ValueError;

/**
 * Разбор и валидация JSON-документа «история цен».
 *
 * Допущения (фиксированы в PR_DESCRIPTION.md):
 *  — отсутствуют секции providers/models/prices считаются пустыми
 *    (файл коррекции при репрайсе может их не содержать);
 *  — неизвестные поля игнорируются;
 *  — дубли (provider, model, valid_from) внутри одного документа разрешаются
 *    в пользу последней записи (семантика upsert);
 *  — currency по умолчанию 'USD' (в спецификации везде USD).
 */
final class PriceFileParser
{
    /** @throws FileFormatException */
    public static function fromJson(string $json): PriceDocument
    {
        $data = json_decode($json, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw FileFormatException::invalidFile('JSON', 'некорректный JSON: '.json_last_error_msg());
        }
        if (!is_array($data)) {
            throw FileFormatException::invalidFile('JSON', 'на верхнем уровне ожидается объект');
        }

        $providersRaw = self::requireSection($data, 'providers');
        $modelsRaw = self::requireSection($data, 'models');
        $pricesRaw = self::requireSection($data, 'prices');

        $providers = self::parseProviders($providersRaw);
        $models = self::parseModels($modelsRaw);
        $prices = self::parsePrices($pricesRaw, $providers, $models);

        return new PriceDocument($providers, $models, $prices);
    }

    /** @param array{0: list<ProviderSpec>, 1: list<ModelSpec>} $parsedSections */
    private static function parsePrices(array $raw, array $providers, array $models): array
    {
        /** @var list<PriceRowSpec> $specs */
        $specs = [];

        foreach ($raw as $i => $row) {
            $ctx = sprintf('prices[%d]', $i);
            if (!is_array($row)) {
                throw FileFormatException::invalidFile($ctx, 'ожидается объект');
            }

            $provider = self::nonEmptyString($row, 'provider', $ctx);
            $model = self::nonEmptyString($row, 'model', $ctx);
            $validFrom = self::utcTimestamp($row, 'valid_from', $ctx);

            $currency = $row['currency'] ?? 'USD';
            if (!is_string($currency) || $currency === '') {
                throw FileFormatException::invalidFile($ctx, '"currency" должна быть непустой строкой');
            }

            if (!array_key_exists('unit', $row) || !is_string($row['unit'])) {
                throw FileFormatException::invalidFile($ctx, '"unit" должна быть строкой');
            }
            try {
                $unit = PriceUnit::from($row['unit']);
            } catch (ValueError) {
                throw FileFormatException::invalidFile(
                    $ctx,
                    sprintf('неизвестный unit "%s" (допустимы per_1m_tokens, per_1m_tokens_unified)', $row['unit']),
                );
            }

            $ratesRaw = $row['rates'] ?? null;
            if (!is_array($ratesRaw)) {
                throw FileFormatException::invalidFile($ctx, '"rates" должен быть объектом со ставками');
            }

            $rates = [];
            foreach ($unit->expectedRateKinds() as $kind) {
                $key = $kind->value;
                if (!array_key_exists($key, $ratesRaw)) {
                    throw FileFormatException::invalidFile($ctx, sprintf('отсутствует ставка "%s" для unit "%s"', $key, $unit->value));
                }
                $rates[$key] = self::decimal($ratesRaw, $key, $ctx.'.rates', '0');
            }

            $specs[] = new PriceRowSpec($provider, $model, $validFrom, $currency, $unit, $rates);
        }

        return $specs;
    }

    /** @return list<ModelSpec> */
    private static function parseModels(array $raw): array
    {
        $specs = [];
        $seen = [];

        foreach ($raw as $i => $row) {
            $ctx = sprintf('models[%d]', $i);
            if (!is_array($row)) {
                throw FileFormatException::invalidFile($ctx, 'ожидается объект');
            }

            $provider = self::nonEmptyString($row, 'provider', $ctx);
            $code = self::nonEmptyString($row, 'code', $ctx);
            $key = $provider."\0".$code;
            if (isset($seen[$key])) {
                throw FileFormatException::invalidFile($ctx, sprintf('модель "%s" у провайдера "%s" задана несколько раз', $code, $provider));
            }
            $seen[$key] = true;

            $specs[] = new ModelSpec($provider, $code);
        }

        return $specs;
    }

    /** @return list<ProviderSpec> */
    private static function parseProviders(array $raw): array
    {
        $specs = [];
        $seen = [];

        foreach ($raw as $i => $row) {
            $ctx = sprintf('providers[%d]', $i);
            if (!is_array($row)) {
                throw FileFormatException::invalidFile($ctx, 'ожидается объект');
            }

            $code = self::nonEmptyString($row, 'code', $ctx);
            if (isset($seen[$code])) {
                throw FileFormatException::invalidFile($ctx, sprintf('код провайдера "%s" задан несколько раз', $code));
            }
            $seen[$code] = true;

            if (!array_key_exists('name', $row) || !is_string($row['name']) || $row['name'] === '') {
                throw FileFormatException::invalidFile($ctx, '"name" должна быть непустой строкой');
            }

            $specs[] = new ProviderSpec($code, $row['name'], self::parseOffPeak($row, $ctx));
        }

        return $specs;
    }

    private static function parseOffPeak(array $row, string $ctx): ?OffPeakWindow
    {
        if (!array_key_exists('off_peak', $row)) {
            return null;
        }

        $op = $row['off_peak'];
        if ($op === null) {
            return null;
        }
        if (!is_array($op)) {
            throw FileFormatException::invalidFile($ctx.'.off_peak', 'ожидается объект или null');
        }

        $start = self::windowTime($op, 'window_start', $ctx);
        $end = self::windowTime($op, 'window_end', $ctx);
        $multiplier = self::positiveDecimal($op, 'multiplier', $ctx.'.off_peak');

        return new OffPeakWindow($start, $end, $multiplier);
    }

    private static function windowTime(array $op, string $field, string $ctx): int
    {
        $value = $op[$field] ?? null;
        if (!is_string($value) || !preg_match('/^([01]?\d|2[0-3]):([0-5]\d)$/', $value, $m)) {
            throw FileFormatException::invalidFile($ctx.'.off_peak.'.$field, sprintf('ожидается "HH:MM" (UTC), а получено "%s"', is_string($value) ? $value : 'не строка'));
        }

        return ((int) $m[1]) * 60 + ((int) $m[2]);
    }

    private static function requireSection(array $data, string $name): array
    {
        if (!array_key_exists($name, $data)) {
            return [];
        }

        $section = $data[$name];
        if (!is_array($section)) {
            throw FileFormatException::invalidFile($name, 'секция должна быть массивом');
        }

        return $section;
    }

    private static function nonEmptyString(array $row, string $field, string $ctx): string
    {
        $value = $row[$field] ?? null;
        if (!is_string($value) || $value === '') {
            throw FileFormatException::invalidFile($ctx, sprintf('поле "%s" должно быть непустой строкой', $field));
        }

        return $value;
    }

    private static function utcTimestamp(array $row, string $field, string $ctx): DateTimeImmutable
    {
        $value = $row[$field] ?? null;
        if (!is_string($value)) {
            throw FileFormatException::invalidFile($ctx, sprintf('поле "%s" должно быть строкой ISO-8601 (UTC)', $field));
        }

        try {
            return Iso8601Utc::parse($value);
        } catch (Exception $e) {
            throw FileFormatException::invalidFile($ctx, sprintf('поле "%s": %s', $field, $e->getMessage()));
        }
    }

    /** Отрицательные значения не допускаются; возвращает десятичную строку. */
    private static function decimal(array $src, string $key, string $ctx, string $min): string
    {
        return self::signedDecimal($src, $key, $ctx, $min);
    }

    /** Строго положительная десятичная строка. */
    private static function positiveDecimal(array $src, string $key, string $ctx): string
    {
        $value = self::signedDecimal($src, $key, $ctx, '0');
        if (bccomp($value, '0', 12) <= 0) {
            throw FileFormatException::invalidFile($ctx, sprintf('поле "%s" должно быть больше нуля', $key));
        }

        return $value;
    }

    private static function signedDecimal(array $src, string $key, string $ctx, string $min): string
    {
        $value = $src[$key] ?? null;
        if (!is_string($value) && !is_int($value)) {
            throw FileFormatException::invalidFile($ctx, sprintf('ставка "%s" должна быть десятичной строкой', $key));
        }

        $value = trim((string) $value);
        if (!preg_match('/^-?\d+(\.\d+)?$/', $value)) {
            throw FileFormatException::invalidFile($ctx, sprintf('ставка "%s": некорректное десятичное число "%s"', $key, $value));
        }
        if ($value[0] === '+') {
            $value = substr($value, 1);
        }
        if (bccomp($value, $min, 12) < 0) {
            throw FileFormatException::invalidFile($ctx, sprintf('ставка "%s" должна быть ≥ "%s"', $key, $min));
        }

        return $value;
    }
}
