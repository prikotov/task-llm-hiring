<?php

declare(strict_types=1);

namespace App\Importer;

use App\Domain\Catalog\RateUnit;
use App\Domain\ValueObject\OffPeak;
use App\Support\DateTimeParser;
use App\Support\Decimal;

use function file_get_contents;

/**
 * Разбор общего JSON-формата «история цен» (схема одинакова для импорта цен
 * и для файла ретро-коррекции).
 *
 * Результат:
 *  - providers: code => ['name' => string, 'offPeak' => ?OffPeak]
 *  - models: "provider|code" => true (множество)
 *  - prices: list<PriceSpec> — PriceSpec:
 *    ['provider', 'model', 'validFrom' => \DateTimeImmutable, 'currency',
 *     'unit' => RateUnit, 'rates' => array<string,string> (6 знаков),
 *     'key' => "provider|model|unixts"]
 */
final class PriceFile
{
    /** @param array<string, array{name: string, offPeak: ?OffPeak}> $providers */
    public function __construct(
        public readonly array $providers,
        public readonly array $models,
        public readonly array $prices,
    ) {
    }

    /**
     * @param bool $requirePrices в режиме импорта секция prices обязательна;
     *                            в режиме коррекции пустая секция допустима.
     *
     * @throws ImportException
     */
    public static function parse(string $path, bool $requirePrices = true): self
    {
        if (!is_readable($path)) {
            throw new ImportException("файл не найден или нечитаем: $path");
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new ImportException("не удалось прочитать файл: $path");
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            throw new ImportException('некорректный JSON: ожидается объект верхнего уровня');
        }

        foreach (['providers', 'models', 'prices'] as $section) {
            if (isset($data["$section"]) && !self::isList($data["$section"])) {
                throw new ImportException("$section: ожидается массив");
            }
        }

        $sections = [];
        foreach (['providers', 'models', 'prices'] as $name) {
            $sections[$name] = $data[$name] ?? [];
        }

        if ($requirePrices && $sections['prices'] === []) {
            throw new ImportException('prices: секция обязательна и не может быть пустой');
        }

        return new self(
            self::parseProviders($sections['providers']),
            self::parseModels($sections['models']),
            self::parsePrices($sections['prices']),
        );
    }

    /**
     * @param list<mixed> $rows
     *
     * @return array<string, array{name: string, offPeak: ?OffPeak}>
     *
     * @throws ImportException
     */
    private static function parseProviders(array $rows): array
    {
        $providers = [];
        foreach ($rows as $i => $p) {
            $ctx = sprintf('providers[%d]', $i);
            if (!is_array($p)) {
                throw new ImportException("$ctx: ожидается объект");
            }
            $code = self::requireNonEmptyString($p, 'code', $ctx);
            $name = self::requireNonEmptyString($p, 'name', $ctx);
            if (isset($providers["$code"])) {
                throw new ImportException("$ctx: дублирующийся code \"$code\"");
            }
            $offPeakSource = $p['off_peak'] ?? null;
            if ($offPeakSource !== null && !is_array($offPeakSource)) {
                throw new ImportException("$ctx.off_peak: ожидается объект или null");
            }
            $providers["$code"] = ['name' => $name, 'offPeak' => OffPeak::create($offPeakSource)];
        }

        return $providers;
    }

    /**
     * @param list<mixed> $rows
     *
     * @return array<string, bool> множество "provider|code"
     *
     * @throws ImportException
     */
    private static function parseModels(array $rows): array
    {
        $models = [];
        foreach ($rows as $i => $m) {
            $ctx = sprintf('models[%d]', $i);
            if (!is_array($m)) {
                throw new ImportException("$ctx: ожидается объект");
            }
            $code = self::requireNonEmptyString($m, 'code', $ctx);
            $provider = self::requireNonEmptyString($m, 'provider', $ctx);
            // Провайдер модели может отсутствовать в этом файле (режим
            // коррекции): проверка существования — на стороне БД.
            $key = $provider.'|'.$code;
            if (isset($models["$key"])) {
                throw new ImportException("$ctx: дублирующаяся модель \"$key\"");
            }
            $models["$key"] = true;
        }

        return $models;
    }

    /**
     * @param list<mixed> $rows
     *
     * @throws ImportException
     */
    private static function parsePrices(array $rows): array
    {
        $prices = [];
        foreach ($rows as $i => $p) {
            $ctx = sprintf('prices[%d]', $i);
            if (!is_array($p)) {
                throw new ImportException("$ctx: ожидается объект");
            }
            $provider = self::requireNonEmptyString($p, 'provider', $ctx);
            $model = self::requireNonEmptyString($p, 'model', $ctx);
            $validFrom = DateTimeParser::parseUtc("$ctx.valid_from", self::requireNonEmptyString($p, 'valid_from', $ctx));
            $currency = self::requireNonEmptyString($p, 'currency', $ctx);
            if (strlen($currency) > 3) {
                throw new ImportException("$ctx.currency: код валюты длиннее 3 символов");
            }
            $unit = RateUnit::tryFrom(self::requireNonEmptyString($p, 'unit', $ctx));
            if ($unit === null) {
                throw new ImportException("$ctx: неизвестный unit");
            }
            if (!isset($p['rates']) || !is_array($p['rates'])) {
                throw new ImportException("$ctx.rates: ожидается объект");
            }
            $rates = [];
            foreach ($unit->rateKeys() as $kind) {
                $rates[$kind] = Decimal::normalize6(
                    Decimal::validate("$ctx.rates[$kind]", $p['rates'][$kind] ?? null),
                );
            }

            $key = $provider.'|'.$model.'|'.$validFrom->getTimestamp();
            if (isset($prices[$key])) {
                throw new ImportException("$ctx: дублирующаяся строка цен (provider, model, valid_from)");
            }
            $prices[$key] = [
                'provider' => $provider,
                'model' => $model,
                'validFrom' => $validFrom,
                'currency' => $currency,
                'unit' => $unit,
                'rates' => $rates,
            ];
        }

        return array_values($prices);
    }

    /**
     * @param array<string, mixed> $row
     */
    private static function requireNonEmptyString(array $row, string $field, string $ctx): string
    {
        $value = $row[$field] ?? null;
        if (!is_string($value) || $value === '') {
            throw new ImportException("$ctx.$field: ожидается непустая строка");
        }

        return $value;
    }

    /**
     * @param mixed $value
     */
    private static function isList($value): bool
    {
        return is_array($value) && array_is_list($value);
    }
}
