<?php

declare(strict_types=1);

namespace App\Ingest;

use App\Domain\TimeWindow;
use App\Domain\Unit;
use App\Ingest\Defs\ModelDef;
use App\Ingest\Defs\PriceDef;
use App\Ingest\Defs\ProviderDef;
use App\Ingest\Defs\UsageEventDef;
use App\Support\Iso8601;

/**
 * Разбор и структурная валидация файла каталога цен (JSON) и
 * отдельного события использования (JSONL-строка).
 *
 * Взаимоссылки «существует в файле или в базе» проверяет сервис загрузки.
 */
final class PriceFileParser
{
    /** @return array{providers: ProviderDef[], models: ModelDef[], prices: PriceDef[]} */
    public function parse(string $rawJson): array
    {
        try {
            $data = json_decode($rawJson, true, 128, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new IngestError('Некорректный JSON: '.$e->getMessage());
        }

        if (!is_array($data)) {
            throw new IngestError('Корень файла должен быть объектом JSON');
        }
        if ($data !== [] && array_is_list($data)) {
            throw new IngestError('Корень файла должен быть объектом JSON, а не массивом значений');
        }

        foreach (['providers', 'models', 'prices'] as $section) {
            if (array_key_exists($section, $data) && !is_array($data[$section])) {
                throw new IngestError(sprintf('Секция "%s" должна быть массивом', $section));
            }
        }

        $providers = [];
        foreach ($data['providers'] ?? [] as $i => $row) {
            $providers[] = $this->parseProvider($row, $i);
        }

        $models = [];
        foreach ($data['models'] ?? [] as $i => $row) {
            $models[] = $this->parseModel($row, $i);
        }

        $prices = [];
        foreach ($data['prices'] ?? [] as $i => $row) {
            $prices[] = $this->parsePrice($row, $i);
        }

        return ['providers' => $providers, 'models' => $models, 'prices' => $prices];
    }

    /** Разбор одной строки JSONL (событие использования). */
    public function parseUsageLine(string $line): UsageEventDef
    {
        try {
            $data = json_decode($line, true, 128, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new IngestError('Некорректный JSON: '.$e->getMessage());
        }

        if (!is_array($data)) {
            throw new IngestError('Событие должно быть объектом JSON');
        }

        return new UsageEventDef(
            $this->nonEmptyString($data['provider'] ?? null, 'provider'),
            $this->nonEmptyString($data['provider_request_id'] ?? null, 'provider_request_id'),
            $this->nonEmptyString($data['model'] ?? null, 'model'),
            $this->nonEmptyString($data['user'] ?? null, 'user'),
            $this->parseTs($data['ts'] ?? null, 'ts'),
            $this->tokenCount($data['tokens_input'] ?? 0, 'tokens_input'),
            $this->tokenCount($data['tokens_output'] ?? 0, 'tokens_output'),
            $this->tokenCount($data['tokens_cache_read'] ?? 0, 'tokens_cache_read'),
            $this->tokenCount($data['tokens_cache_write'] ?? 0, 'tokens_cache_write'),
        );
    }

    /** @param mixed $value */
    private function parseTs($value, string $path): \DateTimeImmutable
    {
        if (!is_string($value)) {
            throw new IngestError(sprintf('%s: ожидается строка ISO-8601 (UTC, Z)', $path));
        }

        try {
            return Iso8601::parseUtc($value);
        } catch (\InvalidArgumentException $e) {
            throw new IngestError(sprintf('%s: %s', $path, $e->getMessage()));
        }
    }

    /** @param mixed $row */
    private function parseProvider($row, int $i): ProviderDef
    {
        if (!is_array($row)) {
            throw new IngestError(sprintf('providers[%d]: объект обязателен', $i));
        }

        $code = $this->nonEmptyString($row['code'] ?? null, sprintf('providers[%d].code', $i));
        $name = $this->nonEmptyString($row['name'] ?? null, sprintf('providers[%d].name', $i));

        $offPeak = $row['off_peak'] ?? null;
        if ($offPeak === null) {
            return new ProviderDef($code, $name, null, null, null);
        }
        if (!is_array($offPeak)) {
            throw new IngestError(sprintf('providers[%d].off_peak: null или объект обязателен', $i));
        }

        $start = $this->nonEmptyString($offPeak['window_start'] ?? null, sprintf('providers[%d].off_peak.window_start', $i));
        $end = $this->nonEmptyString($offPeak['window_end'] ?? null, sprintf('providers[%d].off_peak.window_end', $i));
        $multiplier = $this->nonEmptyString($offPeak['multiplier'] ?? null, sprintf('providers[%d].off_peak.multiplier', $i));

        $this->assertHhMm($start, sprintf('providers[%d].off_peak.window_start', $i));
        $this->assertHhMm($end, sprintf('providers[%d].off_peak.window_end', $i));
        $this->assertDecimal($multiplier, sprintf('providers[%d].off_peak.multiplier', $i));

        return new ProviderDef($code, $name, $start, $end, $multiplier);
    }

    /** @param mixed $row */
    private function parseModel($row, int $i): ModelDef
    {
        if (!is_array($row)) {
            throw new IngestError(sprintf('models[%d]: объект обязателен', $i));
        }

        return new ModelDef(
            $this->nonEmptyString($row['provider'] ?? null, sprintf('models[%d].provider', $i)),
            $this->nonEmptyString($row['code'] ?? null, sprintf('models[%d].code', $i)),
        );
    }

    /** @param mixed $row */
    private function parsePrice($row, int $i): PriceDef
    {
        if (!is_array($row)) {
            throw new IngestError(sprintf('prices[%d]: объект обязателен', $i));
        }

        $provider = $this->nonEmptyString($row['provider'] ?? null, sprintf('prices[%d].provider', $i));
        $model = $this->nonEmptyString($row['model'] ?? null, sprintf('prices[%d].model', $i));
        try {
            $validFrom = Iso8601::parseUtc($this->nonEmptyString($row['valid_from'] ?? null, sprintf('prices[%d].valid_from', $i)));
        } catch (\InvalidArgumentException $e) {
            throw new IngestError(sprintf('prices[%d].valid_from: %s', $i, $e->getMessage()));
        }
        $currency = $this->nonEmptyString($row['currency'] ?? null, sprintf('prices[%d].currency', $i));
        $unit = $this->nonEmptyString($row['unit'] ?? null, sprintf('prices[%d].unit', $i));

        if (!Unit::isValid($unit)) {
            throw new IngestError(sprintf('prices[%d].unit: неизвестная схема "%s"', $i, $unit));
        }

        $ratesRaw = $row['rates'] ?? null;
        if (!is_array($ratesRaw)) {
            throw new IngestError(sprintf('prices[%d].rates: объект обязателен', $i));
        }

        $rates = [];
        foreach (Unit::rateKeys($unit) as $key) {
            if (!array_key_exists($key, $ratesRaw)) {
                throw new IngestError(sprintf('prices[%d].rates: отсутствует "%s" (схема %s)', $i, $key, $unit));
            }
            $value = $ratesRaw[$key];
            if (!is_string($value)) {
                throw new IngestError(sprintf('prices[%d].rates.%s: ожидается строка-десятичная', $i, $key));
            }
            $this->assertDecimal($value, sprintf('prices[%d].rates.%s', $i, $key));
            $rates[$key] = $value;
        }

        $unknown = array_diff(array_keys($ratesRaw), Unit::rateKeys($unit));
        if ($unknown !== []) {
            throw new IngestError(sprintf('prices[%d].rates: неизвестные ключи "%s" (схема %s)', $i, implode('", "', $unknown), $unit));
        }

        return new PriceDef($provider, $model, $validFrom, $currency, $unit, $rates);
    }

    private function nonEmptyString(mixed $value, string $path): string
    {
        if (!is_string($value) || $value === '') {
            throw new IngestError(sprintf('%s: ожидается непустая строка', $path));
        }

        return $value;
    }

    /**
     * @param mixed $value JSON-значение количества токенов
     */
    private function tokenCount($value, string $path): int
    {
        if (is_int($value)) {
            $count = $value;
        } elseif (is_float($value) && $value >= 0 && floor($value) === $value) {
            $count = (int) $value;
        } else {
            throw new IngestError(sprintf('%s: ожидается целое число ≥ 0', $path));
        }
        if ($count < 0) {
            throw new IngestError(sprintf('%s: ожидается целое число ≥ 0', $path));
        }

        return $count;
    }

    private function assertHhMm(string $value, string $path): void
    {
        if (!preg_match(TimeWindow::HH_MM, $value)
            || (int) substr($value, 0, 2) > 23
            || (int) substr($value, 4, 2) > 59
        ) {
            throw new IngestError(sprintf('%s: формат HH:MM (UTC)', $path));
        }
    }

    private function assertDecimal(string $value, string $path): void
    {
        if (!preg_match('/^\d+(\.\d{1,16})?$/', $value)) {
            throw new IngestError(sprintf('%s: ожидается строка с не отрицательным десятичным значением, например "1.500000"', $path));
        }
    }
}
