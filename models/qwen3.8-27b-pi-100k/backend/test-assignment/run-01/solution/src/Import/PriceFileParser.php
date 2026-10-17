<?php

declare(strict_types=1);

namespace App\Import;

use App\Billing\OffPeak;
use App\Entity\PriceRow;
use App\Support\Decimals;
use App\Support\Iso8601;

/**
 * Разбор и проверка файла истории цен (JSON).
 *
 * Политика: структура и каждое поле проверяются строго; дубли секций в
 * одном файле разрешаются как upsert («последний записанный побеждает»).
 * Любая некорректность — ImportException → код возврата команды 1.
 */
final class PriceFileParser
{
    public function parseFile(string $path): PriceFile
    {
        if (!is_file($path) || !is_readable($path)) {
            throw new ImportException(sprintf('Файл не найден или недоступен для чтения: "%s"', $path));
        }

        $raw = file_get_contents($path);
        if ($raw === false) {
            throw new ImportException(sprintf('Не удалось прочитать файл: "%s"', $path));
        }

        $data = json_decode($raw, true);
        if (!is_array($data)) {
            throw new ImportException('Некорректный JSON в файле цен: ожидается JSON-объект');
        }

        return $this->parseData($data);
    }

    public function parseData(array $data): PriceFile
    {
        foreach (['providers', 'models', 'prices'] as $section) {
            if (!array_key_exists($section, $data) || !is_array($data[$section])) {
                throw new ImportException(
                    sprintf('В файле цен отсутствует корректная секция "%s" (массив)', $section),
                );
            }
        }

        $providers = [];
        foreach ($data['providers'] as $i => $row) {
            $spec = $this->parseProvider($row, $i);
            $providers[$spec->getCode()] = $spec;
        }

        $models = [];
        foreach ($data['models'] as $i => $row) {
            $spec = $this->parseModel($row, $i);
            if (!isset($providers[$spec->getProviderCode()])) {
                throw new ImportException(
                    sprintf('models[%d]: провайдер "%s" отсутствует в секции "providers" файла', $i, $spec->getProviderCode()),
                );
            }
            $models[$spec->getProviderCode().'|'.$spec->getCode()] = $spec;
        }

        $prices = [];
        foreach ($data['prices'] as $i => $row) {
            $spec = $this->parsePrice($row, $i);
            if (!isset($models[$spec->getProviderCode().'|'.$spec->getModel()])) {
                throw new ImportException(
                    sprintf(
                        'prices[%d]: модель "%s/%s" отсутствует в секции "models" файла',
                        $i,
                        $spec->getProviderCode(),
                        $spec->getModel(),
                    ),
                );
            }
            $key = $spec->getProviderCode().'|'.$spec->getModel().'|'.(int) $spec->getValidFrom()->format('U');
            $prices[$key] = $spec;
        }

        return new PriceFile($providers, $models, $prices);
    }

    /** @param mixed $row */
    private function parseProvider(mixed $row, int $index): ProviderSpec
    {
        if (!is_array($row)) {
            throw new ImportException(sprintf('providers[%d]: ожидается объект', $index));
        }

        $code = $this->nonEmptyString($row['code'] ?? null, sprintf('providers[%d].code', $index), 64);
        $name = $this->nonEmptyString($row['name'] ?? null, sprintf('providers[%d].name', $index), 255);

        $offPeak = null;
        if (array_key_exists('off_peak', $row) && $row['off_peak'] !== null) {
            $raw = $row['off_peak'];
            if (!is_array($raw)) {
                throw new ImportException(sprintf('providers[%d].off_peak: ожидается объект или null', $index));
            }

            $start = $this->nonEmptyString($raw['window_start'] ?? null, sprintf('providers[%d].off_peak.window_start', $index), 5);
            $end = $this->nonEmptyString($raw['window_end'] ?? null, sprintf('providers[%d].off_peak.window_end', $index), 5);
            $multiplier = $this->decimal($raw['multiplier'] ?? null, sprintf('providers[%d].off_peak.multiplier', $index));
            if ($multiplier === '0') {
                throw new ImportException(sprintf('providers[%d].off_peak.multiplier: должен быть положительным', $index));
            }

            try {
                new OffPeak($start, $end, $multiplier); // валидация формата времени
            } catch (\InvalidArgumentException $e) {
                throw new ImportException(sprintf('providers[%d].off_peak: %s', $index, $e->getMessage()));
            }

            $offPeak = ['windowStart' => $start, 'windowEnd' => $end, 'multiplier' => $multiplier];
        }

        return new ProviderSpec($code, $name, $offPeak);
    }

    /** @param mixed $row */
    private function parseModel(mixed $row, int $index): ModelSpec
    {
        if (!is_array($row)) {
            throw new ImportException(sprintf('models[%d]: ожидается объект', $index));
        }

        $provider = $this->nonEmptyString($row['provider'] ?? null, sprintf('models[%d].provider', $index), 64);
        $code = $this->nonEmptyString($row['code'] ?? null, sprintf('models[%d].code', $index), 64);

        return new ModelSpec($provider, $code);
    }

    /** @param mixed $row */
    private function parsePrice(mixed $row, int $index): PriceSpec
    {
        if (!is_array($row)) {
            throw new ImportException(sprintf('prices[%d]: ожидается объект', $index));
        }

        $provider = $this->nonEmptyString($row['provider'] ?? null, sprintf('prices[%d].provider', $index), 64);
        $model = $this->nonEmptyString($row['model'] ?? null, sprintf('prices[%d].model', $index), 64);

        try {
            $validFrom = Iso8601::parseUtc((string) ($row['valid_from'] ?? ''));
        } catch (\InvalidArgumentException) {
            throw new ImportException(sprintf('prices[%d].valid_from: ожидалась ISO-8601 дата в UTC', $index));
        }

        $currency = $this->nonEmptyString($row['currency'] ?? null, sprintf('prices[%d].currency', $index), 3);
        if ($currency !== 'USD') {
            throw new ImportException(sprintf('prices[%d].currency: поддерживается только "USD" (получено "%s")', $index, $currency));
        }

        $unit = $this->nonEmptyString($row['unit'] ?? null, sprintf('prices[%d].unit', $index), 32);
        if (!in_array($unit, [PriceRow::UNIT_PER_TOKEN, PriceRow::UNIT_PER_TOKEN_UNIFIED], true)) {
            throw new ImportException(
                sprintf('prices[%d].unit: неизвестная схема тарификации "%s"', $index, $unit),
            );
        }

        $ratesRaw = $row['rates'] ?? null;
        if (!is_array($ratesRaw)) {
            throw new ImportException(sprintf('prices[%d].rates: ожидается объект', $index));
        }

        $expectedKeys = $unit === PriceRow::UNIT_PER_TOKEN_UNIFIED
            ? [PriceRow::UNIFIED_RATE_KEY]
            : PriceRow::PER_TOKEN_RATE_KEYS;

        $rates = [];
        foreach ($expectedKeys as $key) {
            if (!array_key_exists($key, $ratesRaw)) {
                throw new ImportException(sprintf('prices[%d].rates: отсутствует ставка "%s"', $index, $key));
            }
            $rates[$key] = $this->decimal($ratesRaw[$key], sprintf('prices[%d].rates.%s', $index, $key));
        }
        $extra = array_diff(array_keys($ratesRaw), $expectedKeys);
        if ($extra !== []) {
            throw new ImportException(
                sprintf('prices[%d].rates: лишние ставки: %s', $index, implode(', ', $extra)),
            );
        }

        return new PriceSpec($provider, $model, $validFrom, $currency, $unit, $rates);
    }

    /** @param mixed $value */
    private function nonEmptyString(mixed $value, string $where, int $maxLength): string
    {
        if (!is_string($value) || $value === '' || mb_strlen($value) > $maxLength) {
            throw new ImportException(sprintf('%s: ожидается непустая строка (не длиннее %d символов)', $where, $maxLength));
        }

        return $value;
    }

    /** @param mixed $value */
    private function decimal(mixed $value, string $where): string
    {
        if (!is_string($value)) {
            throw new ImportException(sprintf('%s: ожидается десятичная строка (напр. "3.000000"), а не %s', $where, get_debug_type($value)));
        }
        try {
            $normalized = Decimals::normalize($value);
        } catch (\InvalidArgumentException) {
            throw new ImportException(sprintf('%s: ожидалось неотрицательное десятичное число, получено "%s"', $where, $value));
        }
        if (bccomp($normalized, '0', 0) < 0) {
            throw new ImportException(sprintf('%s: значение не может быть отрицательным', $where));
        }

        return $normalized;
    }
}
