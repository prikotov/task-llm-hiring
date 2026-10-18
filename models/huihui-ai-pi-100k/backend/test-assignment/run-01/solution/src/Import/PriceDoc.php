<?php

declare(strict_types=1);

namespace App\Import;

use App\Domain\OffPeakWindow;
use App\Domain\Unit;
use App\Util\DateTimeUtc;
use DomainException;
use JsonException;

/**
 * Разобранный и проверенный файл цен (секции providers/models/prices).
 * Секции необязательны: отсутствующая считается пустой (актуально для
 * файла ретро-коррекции app:billing:reprice).
 */
final readonly class PriceDoc
{
    /**
     * @param list<ProviderDoc>   $providers
     * @param list<ModelDoc>      $models
     * @param list<PriceRateDoc> $rates
     */
    public function __construct(
        public array $providers = [],
        public array $models = [],
        public array $rates = [],
    ) {
    }

    /**
     * @throws ImportException
     * @throws DomainException
     */
    public static function fromFile(string $path): self
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new ImportException(sprintf('Файл не найден или недоступен: "%s".', $path));
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new ImportException(sprintf('Не удалось прочитать файл: "%s".', $path));
        }

        /** @var mixed $data */
        try {
            $data = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException $e) {
            throw new ImportException(sprintf('Некорректный JSON: %s', $e->getMessage()), 0, $e);
        }

        if (!is_array($data)) {
            throw new ImportException('Ожидался JSON-объект на верхнем уровне.');
        }

        return new self(
            self::parseProviders($data['providers'] ?? []),
            self::parseModels($data['models'] ?? []),
            self::parseRates($data['prices'] ?? []),
        );
    }

    /**
     * @param mixed $section
     *
     * @return list<ProviderDoc>
     *
     * @throws ImportException
     * @throws DomainException
     */
    private static function parseProviders($section): array
    {
        if (!is_array($section)) {
            throw new ImportException('Секция "providers" должна быть массивом.');
        }

        $out = [];
        foreach ($section as $i => $item) {
            if (!is_array($item)) {
                throw new ImportException(sprintf('providers[%d]: ожидался объект.', $i));
            }

            $code = self::stringOf($item, 'code', 'providers', $i);
            $name = self::stringOf($item, 'name', 'providers', $i);

            $offPeak = null;
            if (array_key_exists('off_peak', $item) && $item['off_peak'] !== null) {
                if (!is_array($item['off_peak'])) {
                    throw new ImportException(sprintf('providers[%d]: "off_peak" должен быть объектом либо null.', $i));
                }

                $whereOff = 'providers[' . $i . '].off_peak';
                $offPeak = OffPeakWindow::create(
                    self::stringOf((array) $item['off_peak'], 'window_start', $whereOff, $i),
                    self::stringOf((array) $item['off_peak'], 'window_end', $whereOff, $i),
                    self::stringOf((array) $item['off_peak'], 'multiplier', $whereOff, $i),
                );
            }

            $out[] = new ProviderDoc($code, $name, $offPeak);
        }

        return $out;
    }

    /**
     * @param mixed $section
     *
     * @return list<ModelDoc>
     *
     * @throws ImportException
     */
    private static function parseModels($section): array
    {
        if (!is_array($section)) {
            throw new ImportException('Секция "models" должна быть массивом.');
        }

        $out = [];
        foreach ($section as $i => $item) {
            if (!is_array($item)) {
                throw new ImportException(sprintf('models[%d]: ожидался объект.', $i));
            }

            $out[] = new ModelDoc(
                self::stringOf($item, 'provider', 'models', $i),
                self::stringOf($item, 'code', 'models', $i),
            );
        }

        return $out;
    }

    /**
     * @param mixed $section
     *
     * @return list<PriceRateDoc>
     *
     * @throws ImportException
     * @throws DomainException
     */
    private static function parseRates($section): array
    {
        if (!is_array($section)) {
            throw new ImportException('Секция "prices" должна быть массивом.');
        }

        $out = [];
        foreach ($section as $i => $item) {
            if (!is_array($item)) {
                throw new ImportException(sprintf('prices[%d]: ожидался объект.', $i));
            }

            $where = 'prices[' . $i . ']';
            $provider = self::stringOf($item, 'provider', 'prices', $i);
            $model = self::stringOf($item, 'model', 'prices', $i);
            $validFrom = self::dateTimeOf($item, 'valid_from', $where);
            $currency = self::stringOf($item, 'currency', 'prices', $i);
            $unit = Unit::fromWire(self::stringOf($item, 'unit', 'prices', $i));

            if (!isset($item['rates']) || !is_array($item['rates'])) {
                throw new ImportException(sprintf('%s: секция "rates" должна быть объектом.', $where));
            }

            /** @var array<string, mixed> $ratesRaw */
            $ratesRaw = $item['rates'];
            $rates = [];
            foreach ($ratesRaw as $key => $value) {
                if (!is_string($value) || preg_match('/^(0|[1-9]\d*)(\.\d{1,6})?$/', $value) !== 1) {
                    throw new ImportException(sprintf('%s: ставка "%s" должна быть десятичной строкой (не числом JSON) без отрицательного значения и не длиннее 6 знаков.', $where, (string) $key));
                }
                $rates[(string) $key] = $value;
            }

            $required = match ($unit) {
                Unit::PerToken => ['input', 'output', 'cache_read', 'cache_write'],
                Unit::PerTokenUnified => ['any'],
            };
            foreach ($required as $key) {
                if (!array_key_exists($key, $rates)) {
                    throw new ImportException(sprintf('%s: схема "%s" требует ставку "%s".', $where, $unit->value, $key));
                }
            }

            foreach (array_keys($rates) as $key) {
                if (!in_array($key, $required, true)) {
                    throw new ImportException(sprintf('%s: схема "%s" не знает ставки "%s".', $where, $unit->value, (string) $key));
                }
            }

            $out[] = new PriceRateDoc($provider, $model, $validFrom, $currency, $unit, $rates);
        }

        return $out;
    }

    /**
     * @param array<string, mixed> $item
     *
     * @throws ImportException
     */
    private static function stringOf(array $item, string $key, string $where, int $index): string
    {
        $value = $item[$key] ?? null;
        if (!is_string($value) || $value === '') {
            throw new ImportException(sprintf(
                '%s[%d]: поле "%s" отсутствует либо не непустая строка.',
                $where,
                $index,
                $key,
            ));
        }

        return $value;
    }

    /**
     * @param array<string, mixed> $item
     *
     * @throws ImportException
     * @throws DomainException
     */
    private static function dateTimeOf(array $item, string $key, string $where): \DateTimeImmutable
    {
        $value = $item[$key] ?? null;
        if (!is_string($value) || $value === '') {
            throw new ImportException(sprintf('%s: поле "%s" отсутствует либо не строка (ISO-8601).', $where, $key));
        }

        return DateTimeUtc::parse($value);
    }
}
