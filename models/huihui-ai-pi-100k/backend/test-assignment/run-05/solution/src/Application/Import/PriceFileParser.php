<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Application\Exception\InputException;
use App\Domain\Billing\PriceRowSpec;
use App\Support\IsoUtcTime;

use function is_array;
use function is_string;

/**
 * Разбор и валидация JSON-файла справочника цен.
 *
 * Формат — см. TASK.md, раздел «История цен». Секции
 * providers/models опциональны (reprice-файлы), prices обязательна
 * для reprice, но допустима пустой для import? Нет: import тоже
 * принимает файл без цен (просто обновит провайдеров/модели).
 *
 * Допущение: ставка/multiplier — десятичная строка (числова часть
 * и 1–6 знаков после точки). Значения вне этого формата отклоняются.
 */
final class PriceFileParser
{
    private const DECIMAL_RX = '/^\d+(\.\d{1,6})?$/';

    private const HHMM_RX = '/^([01]?\d|2[0-3]):[0-5]\d$/';

    /** @throws InputException при некорректном JSON или данных */
    public function parse(string $path): ParsedPrices
    {
        $raw = self::readFile($path);

        /** @var array<mixed> $data */
        $data = self::decodeJson($raw, $path);

        return new ParsedPrices(
            providers: self::parseProviders($data['providers'] ?? []),
            models: self::parseModels($data['models'] ?? []),
            prices: self::parsePrices($data['prices'] ?? []),
        );
    }

    private static function readFile(string $path): string
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new InputException(sprintf('Файл не найден или нечитаем: %s', $path));
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new InputException(sprintf('Не удалось прочитать файл: %s', $path));
        }

        return $raw;
    }

    /**
     * @return array<mixed>
     */
    private static function decodeJson(string $raw, string $path): array
    {
        /** @var array<mixed> $decoded */
        try {
            $decoded = json_decode($raw, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new InputException(sprintf('%s: некорректный JSON: %s', $path, $e->getMessage()), 0, $e);
        }

        if (!is_array($decoded)) {
            throw new InputException(sprintf('%s: корневой элемент должен быть объектом', $path));
        }

        return $decoded;
    }

    /**
     * @param array<mixed> $rows
     *
     * @return ProviderRecord[]
     */
    private static function parseProviders(array $rows): array
    {
        $result = [];
        foreach ($rows as $i => $row) {
            $label = sprintf('providers[%d]', $i);
            if (!is_array($row)) {
                throw new InputException($label . ': ожидается объект');
            }

            $code = self::requireString($row, 'code', $label, 64);
            $name = self::requireString($row, 'name', $label, 255);

            $offPeak = null;
            if (array_key_exists('off_peak', $row) && $row['off_peak'] !== null) {
                $offPeak = self::parseOffPeak($row['off_peak'], $label);
            }

            $result[] = new ProviderRecord(
                code: $code,
                name: $name,
                offPeakStart: $offPeak[0] ?? null,
                offPeakEnd: $offPeak[1] ?? null,
                offPeakMultiplier: $offPeak[2] ?? null,
            );
        }

        return $result;
    }

    /**
     * @param mixed $v
     *
     * @return array{0:string,1:string,2:string}
     */
    private static function parseOffPeak(mixed $v, string $label): array
    {
        if (!is_array($v)) {
            throw new InputException($label . ': off_peak должен быть объектом или null');
        }

        $start = self::requireString($v, 'window_start', $label . ':off_peak', 5);
        $end = self::requireString($v, 'window_end', $label . ':off_peak', 5);
        $multiplier = self::requireString($v, 'multiplier', $label . ':off_peak', 32);

        if (!preg_match(self::HHMM_RX, $start)) {
            throw new InputException(sprintf(
                '%s: off_peak.window_start — формат ЧЧ:ММ, получено "%s"',
                $label,
                $start,
            ));
        }
        if (!preg_match(self::HHMM_RX, $end)) {
            throw new InputException(sprintf('%s: off_peak.window_end — формат ЧЧ:ММ, получено "%s"', $label, $end));
        }
        if (!preg_match(self::DECIMAL_RX, $multiplier)) {
            throw new InputException(sprintf(
                '%s: off_peak.multiplier — десятичная строка, получено "%s"',
                $label,
                $multiplier,
            ));
        }

        return [$start, $end, $multiplier];
    }

    /**
     * @param array<mixed> $rows
     *
     * @return ModelRecord[]
     */
    private static function parseModels(array $rows): array
    {
        $result = [];
        foreach ($rows as $i => $row) {
            $label = sprintf('models[%d]', $i);
            if (!is_array($row)) {
                throw new InputException($label . ': ожидается объект');
            }

            $result[] = new ModelRecord(
                provider: self::requireString($row, 'provider', $label, 64),
                code: self::requireString($row, 'code', $label, 64),
            );
        }

        return $result;
    }

    /**
     * @param array<mixed> $rows
     *
     * @return PriceRecord[]
     */
    private static function parsePrices(array $rows): array
    {
        $seen = [];
        $result = [];

        foreach ($rows as $i => $row) {
            $label = sprintf('prices[%d]', $i);
            if (!is_array($row)) {
                throw new InputException($label . ': ожидается объект');
            }

            $provider = self::requireString($row, 'provider', $label, 64);
            $model = self::requireString($row, 'model', $label, 64);

            $validFrom = self::requireIsoUtc($row, 'valid_from', $label);
            $currency = self::requireString($row, 'currency', $label, 8);
            $unit = self::requireString($row, 'unit', $label, 32);

            $specs = [PriceRowSpec::UNIT_PER_TOKEN, PriceRowSpec::UNIT_UNIFIED];
            if (!in_array($unit, $specs, true)) {
                throw new InputException(sprintf(
                    '%s: unit должен быть "per_1m_tokens" или "per_1m_tokens_unified", получено "%s"',
                    $label,
                    $unit,
                ));
            }

            $ratesRaw = $row['rates'] ?? null;
            if (!is_array($ratesRaw)) {
                throw new InputException($label . ': rates должен быть объектом');
            }

            $spec = $unit === PriceRowSpec::UNIT_PER_TOKEN
                ? PriceRowSpec::perToken(
                    input: self::requireDecimal($ratesRaw, 'input', $label),
                    output: self::requireDecimal($ratesRaw, 'output', $label),
                    cacheRead: self::requireDecimal($ratesRaw, 'cache_read', $label),
                    cacheWrite: self::requireDecimal($ratesRaw, 'cache_write', $label),
                )
                : PriceRowSpec::unified(self::requireDecimal($ratesRaw, 'any', $label));

            $key = $provider . '|' . $model . '|' . $validFrom->format('Y-m-d H:i:s.v\Z');
            if (isset($seen[$key])) {
                throw new InputException(sprintf(
                    '%s: дублирующийся ключ (provider, model, valid_from)="%s"',
                    $label,
                    $key,
                ));
            }
            $seen[$key] = true;

            $result[] = new PriceRecord(
                provider: $provider,
                model: $model,
                validFrom: $validFrom,
                currency: $currency,
                spec: $spec,
            );
        }

        return $result;
    }

    /**
     * @param array<mixed> $row
     */
    private static function requireString(array $row, string $key, string $label, int $maxLen): string
    {
        $v = $row[$key] ?? null;
        if (!is_string($v) || $v === '' || strlen($v) > $maxLen) {
            throw new InputException(sprintf(
                '%s: поле "%s" — непустая строка (не более %d символов)',
                $label, $key, $maxLen,
            ));
        }

        return $v;
    }

    /**
     * @param array<mixed> $row
     */
    private static function requireIsoUtc(array $row, string $key, string $label): \DateTimeImmutable
    {
        $v = $row[$key] ?? null;
        if (!is_string($v) || $v === '') {
            throw new InputException(sprintf('%s: поле "%s" — строка времени ISO-8601 UTC', $label, $key));
        }

        return IsoUtcTime::parse($v, $label . ':' . $key);
    }

    /**
     * @param array<mixed> $rates
     */
    private static function requireDecimal(array $rates, string $key, string $label): string
    {
        $v = $rates[$key] ?? null;
        if (!is_string($v) || !preg_match(self::DECIMAL_RX, $v)) {
            throw new InputException(sprintf(
                '%s: rates.%s — десятичная строка (числа и 1–6 знаков после точки), получено %s',
                $label,
                $key,
                is_string($v) ? '"' . $v . '"' : get_debug_type($v)
            ));
        }

        return $v;
    }
}
