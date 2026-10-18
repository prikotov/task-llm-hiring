<?php

declare(strict_types=1);

namespace App\Application\Catalog;

use App\Domain\Catalog\OffPeak;
use App\Domain\Catalog\PricingUnit;
use App\Shared\Decimals;
use App\Shared\InputFormatException;
use App\Shared\IsoUtc;
use App\Shared\JsonFile;

/**
 * Чтение и построчная валидация файла истории цен.
 *
 * Здесь проверяется только структура и форматы; разрешимость ссылок
 * (провайдер/модель) — за Application-сервисами: у импорта справочник
 * разрешается «в файле или в БД», у reprice — строго «в БД».
 */
final class CatalogFileLoader
{
    public function __construct(private readonly bool $pricesNonEmptyRequired)
    {
    }

    public function load(string $path): CatalogFile
    {
        $raw = JsonFile::loadObject($path);

        $providers = $this->readProviders(self::section($raw, 'providers', $path), $path);
        $models = $this->readModels(self::section($raw, 'models', $path), $path);
        $prices = $this->readPrices(self::section($raw, 'prices', $path), $path);

        if ($this->pricesNonEmptyRequired && count($prices) === 0) {
            throw new InputFormatException($path.': в файле коррекции нужна хотя бы одна строка цен (prices)');
        }

        return new CatalogFile($providers, $models, $prices);
    }

    private static function section(array $raw, string $key, string $path): array
    {
        if (!array_key_exists($key, $raw)) {
            return [];
        }

        $value = $raw[$key];
        if (!\is_array($value)) {
            throw new InputFormatException($path.': секция "'.$key.'" должен быть массивом записей');
        }

        return $value;
    }

    /** @return list<ProviderSpec> */
    private function readProviders(array $entries, string $path): array
    {
        $specs = [];

        foreach ($entries as $i => $entry) {
            $ctx = $path.':providers['.$i.']';
            self::assertObject($entry, $ctx);

            $code = self::nonEmptyString($entry, 'code', $ctx);
            $name = self::nonEmptyString($entry, 'name', $ctx);

            $offPeak = null;
            if (array_key_exists('off_peak', $entry)) {
                $offPeak = self::readOffPeak($entry['off_peak'], $ctx);
            }

            // Дубль кода в одном файле: действует последняя запись (upsert-семантика).
            $specs[] = new ProviderSpec($code, $name, $offPeak);
        }

        return $specs;
    }

    /** @return list<ModelSpec> */
    private function readModels(array $entries, string $path): array
    {
        $specs = [];

        foreach ($entries as $i => $entry) {
            $ctx = $path.':models['.$i.']';
            self::assertObject($entry, $ctx);

            $specs[] = new ModelSpec(
                self::nonEmptyString($entry, 'provider', $ctx),
                self::nonEmptyString($entry, 'code', $ctx),
            );
        }

        return $specs;
    }

    /** @return list<PriceSpec> */
    private function readPrices(array $entries, string $path): array
    {
        $specs = [];

        foreach ($entries as $i => $entry) {
            $ctx = $path.':prices['.$i.']';
            self::assertObject($entry, $ctx);

            $provider = self::nonEmptyString($entry, 'provider', $ctx);
            $model = self::nonEmptyString($entry, 'model', $ctx);

            $validFromRaw = $entry['valid_from'] ?? null;
            if (!\is_string($validFromRaw)) {
                throw new InputFormatException($ctx.': "valid_from": нужна строка ISO-8601 (UTC)');
            }
            $validFrom = IsoUtc::parse($validFromRaw, $ctx.'.valid_from');

            $currency = self::nonEmptyString($entry, 'currency', $ctx);

            $unitRaw = $entry['unit'] ?? null;
            $unit = \is_string($unitRaw) ? PricingUnit::tryFrom($unitRaw) : null;
            if ($unit === null) {
                throw new InputFormatException($ctx.': "unit": ожидается "per_1m_tokens" или "per_1m_tokens_unified"');
            }

            $ratesRaw = $entry['rates'] ?? null;
            if (!\is_array($ratesRaw) || $ratesRaw === []) {
                throw new InputFormatException($ctx.': "rates": нужен объект со ставками');
            }

            $rates = [];
            foreach ($unit->rateKeys() as $key) {
                if (!array_key_exists($key, $ratesRaw)) {
                    throw new InputFormatException($ctx.": rates: отсутствует ставка \"{$key}\" для схемы {$unit->value}");
                }
                $value = $ratesRaw[$key];
                if (!\is_string($value)) {
                    throw new InputFormatException($ctx.": rates.{$key}: ставка должна быть десятичной строкой (не числом JSON)");
                }
                $rates[$key] = Decimals::validate($value, 6, $ctx.":rates.{$key}");
            }
            foreach (array_keys($ratesRaw) as $extra) {
                if (!\in_array($extra, $unit->rateKeys(), true)) {
                    throw new InputFormatException($ctx.": rates: ставка \"{$extra}\" не входит в схему {$unit->value}");
                }
            }

            $specs[] = new PriceSpec($provider, $model, $validFrom, $currency, $unit, $rates);
        }

        return $specs;
    }

    private static function assertObject(mixed $value, string $ctx): void
    {
        if (!\is_array($value) || array_is_list($value)) {
            throw new InputFormatException($ctx.': запись должна быть объектом JSON');
        }
    }

    private static function nonEmptyString(array $entry, string $key, string $ctx): string
    {
        $value = $entry[$key] ?? null;
        if (!\is_string($value) || trim($value) === '') {
            throw new InputFormatException($ctx.": \"{$key}\": требуется непустая строка");
        }

        return $value;
    }

    private static function readOffPeak(mixed $value, string $ctx): ?OffPeak
    {
        if ($value === null) {
            return null;
        }

        if (!\is_array($value)) {
            throw new InputFormatException($ctx.': "off_peak": ожидается объект или null');
        }

        $start = $value['window_start'] ?? null;
        $end = $value['window_end'] ?? null;
        if (!\is_string($start) || !\is_string($end)) {
            throw new InputFormatException($ctx.': off_peak: нужны window_start и window_end в формате "HH:MM"');
        }

        $multiplierRaw = $value['multiplier'] ?? null;
        if (!\is_string($multiplierRaw)) {
            throw new InputFormatException($ctx.': off_peak: "multiplier" должна быть десятичной строкой');
        }
        $multiplier = Decimals::validate($multiplierRaw, 16, $ctx.':off_peak.multiplier');

        return new OffPeak($start, $end, $multiplier);
    }
}
