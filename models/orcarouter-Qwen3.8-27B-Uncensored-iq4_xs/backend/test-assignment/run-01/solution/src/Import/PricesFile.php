<?php

declare(strict_types=1);

namespace App\Import;

use App\Billing\OffPeakWindow;
use App\Entity\PriceRow;
use App\Exception\ImportException;
use App\Util\MoneyCalculator;
use App\Util\UtcTime;
use DateTimeImmutable;

/**
 * Разобранный файл истории цен (формат `fixtures/prices.json`).
 *
 * Секции providers/models допускаются как отсутствующие/пустые
 * (файл ретро-коррекции цен может содержать только prices).
 */
final class PricesFile
{
    /**
     * @param list<ParsedProvider> $providers
     * @param list<array{provider: string, code: string}> $models
     * @param list<array{provider: string, model: string, valid_from: \DateTimeImmutable, currency: string, unit: string, rates: array<string, string>}> $prices
     */
    public function __construct(
        private readonly array $providers,
        private readonly array $models,
        private readonly array $prices,
    ) {
    }

    /** @return list<ParsedProvider> */
    public function providers(): array
    {
        return $this->providers;
    }

    /** @return list<array{provider: string, code: string}> */
    public function models(): array
    {
        return $this->models;
    }

    /** @return list<array{provider: string, model: string, valid_from: \DateTimeImmutable, currency: string, unit: string, rates: array<string, string>}> */
    public function prices(): array
    {
        return $this->prices;
    }

    public static function fromFile(string $path): self
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new ImportException(sprintf('Файл не найден или нечитаем: %s', $path));
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new ImportException(sprintf('Не удалось прочитать файл: %s', $path));
        }

        try {
            $decoded = json_decode($raw, true, 32, flags: JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new ImportException(sprintf('Некорректный JSON в %s: %s', $path, $e->getMessage()));
        }

        if (!\is_array($decoded)) {
            throw new ImportException('Корень файла истории цен должен быть объектом JSON');
        }

        return self::fromArray($decoded, $path);
    }

    /** @param array<string, mixed> $data */
    public static function fromArray(array $data, string $context = 'файл цен'): self
    {
        $providers = [];
        foreach (self::section($data, 'providers') as $i => $p) {
            $providers[] = ParsedProvider::fromArray(
                self::item($p, 'providers', $i, $context),
                sprintf('%s: providers[%d]', $context, $i),
            );
        }

        $models = [];
        foreach (self::section($data, 'models') as $i => $m) {
            $models[] = self::parseModel(
                self::item($m, 'models', $i, $context),
                sprintf('%s: models[%d]', $context, $i),
            );
        }

        $prices = [];
        foreach (self::section($data, 'prices') as $i => $p) {
            $prices[] = self::parsePrice(
                self::item($p, 'prices', $i, $context),
                sprintf('%s: prices[%d]', $context, $i),
            );
        }

        return new self($providers, $models, $prices);
    }

    /** @param array<string, mixed> $data */
    private static function section(array $data, string $name): array
    {
        if (!\array_key_exists($name, $data)) {
            return [];
        }
        $section = $data[$name];
        if (!\is_array($section)) {
            throw new ImportException(sprintf('Секция "%s" должна быть массивом', $name));
        }

        return $section;
    }

    /** @param array<string, mixed> $data */
    private static function item(mixed $value, string $section, int $index, string $context): array
    {
        if (!\is_array($value)) {
            throw new ImportException(sprintf('%s: %s[%d] должна быть объектом', $context, $section, $index));
        }

        return $value;
    }

    /** @param array<string, mixed> $m @return array{provider: string, code: string} */
    private static function parseModel(array $m, string $ctx): array
    {
        return [
            'provider' => self::nonEmptyString($m, 'provider', $ctx),
            'code' => self::nonEmptyString($m, 'code', $ctx),
        ];
    }

    /**
     * @param array<string, mixed> $p
     * @return array{provider: string, model: string, valid_from: \DateTimeImmutable, currency: string, unit: string, rates: array<string, string>}
     */
    private static function parsePrice(array $p, string $ctx): array
    {
        $provider = self::nonEmptyString($p, 'provider', $ctx);
        $model = self::nonEmptyString($p, 'model', $ctx);
        $validFrom = UtcTime::parse($p['valid_from'] ?? null, $ctx.'.valid_from');
        $currency = self::nonEmptyString($p, 'currency', $ctx);
        $unit = self::nonEmptyString($p, 'unit', $ctx);

        if (!\in_array($unit, [PriceRow::UNIT_PER_TOKEN, PriceRow::UNIT_UNIFIED], true)) {
            throw new ImportException(sprintf('%s: неизвестная схема тарификации unit="%s"', $ctx, $unit));
        }

        return [
            'provider' => $provider,
            'model' => $model,
            'valid_from' => $validFrom,
            'currency' => $currency,
            'unit' => $unit,
            'rates' => self::parseRates($p['rates'] ?? null, $unit, $ctx),
        ];
    }

    private static function parseRates(mixed $rates, string $unit, string $ctx): array
    {
        if (!\is_array($rates)) {
            throw new ImportException(sprintf('%s: rates должен быть объектом со строковыми значениями', $ctx));
        }

        $expectedKeys = $unit === PriceRow::UNIT_PER_TOKEN
            ? PriceRow::TOKEN_KINDS
            : [PriceRow::UNIFIED_RATE_KEY];

        foreach ($expectedKeys as $key) {
            if (!\array_key_exists($key, $rates)) {
                throw new ImportException(sprintf('%s: для unit="%s" отсутствует rate "%s"', $ctx, $unit, $key));
            }
        }
        foreach (array_keys($rates) as $key) {
            if (!\in_array($key, $expectedKeys, true)) {
                throw new ImportException(sprintf('%s: для unit="%s" неожидан rate "%s"', $ctx, $unit, (string) $key));
            }
        }

        $result = [];
        foreach ($rates as $key => $value) {
            if (!\is_string($value)) {
                throw new ImportException(sprintf('%s: rate "%s" должен быть десятичной строкой', $ctx, (string) $key));
            }
            MoneyCalculator::assertDecimal($value);
            $result[(string) $key] = $value;
        }

        return $result;
    }

    /** @param array<string, mixed> $item */
    private static function nonEmptyString(array $item, string $field, string $ctx): string
    {
        $value = $item[$field] ?? null;
        if (!\is_string($value) || $value === '') {
            throw new ImportException(sprintf('%s: поле "%s" — обязательная непустая строка', $ctx, $field));
        }

        return $value;
    }
}
