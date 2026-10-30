<?php

declare(strict_types=1);

namespace App\Application\Import;

/**
 * Парсер истории цен (JSON).
 *
 * Формат:
 *   {"providers": [...], "models": [...], "prices": [...]}
 *
 * Каждая секция может отсутствовать (в этом случае — как пустой массив).
 * Все проверки валидности (ключи, типы формата) — здесь; при обнаружении
 * невалидного файла выкидывается ImportException с человечески понятным
 * сообщением, а команда импорта прерывается.
 */
final class PricesParser
{
    /**
     * @return array{
     *   providers: array<int, array{code: string, name: string, offPeak: null|array{window_start: string, window_end: string, multiplier: string}}>,
     *   models: array<int, array{provider: string, code: string}>,
     *   prices: array<int, array{provider: string, model: string, valid_from: string, currency: string, unit: string, rates: array<string, string>}>
     * }
     *
     * @throws ImportException при невалидном JSON или формата
     */
    public function parse(string $raw): array
    {
        $data = json_decode($raw, true);
        if (!is_array($data)) {
            throw ImportException::json('корневой элемент — объект');
        }

        $out = ['providers' => [], 'models' => [], 'prices' => []];

        $data['providers'] ??= [];
        $data['models'] ??= [];
        $data['prices'] ??= [];

        if (!is_array($data['providers'])) {
            throw ImportException::jsonFormat('providers — массив');
        }
        if (!is_array($data['models'])) {
            throw ImportException::jsonFormat('models — массив');
        }
        if (!is_array($data['prices'])) {
            throw ImportException::jsonFormat('prices — массив');
        }

        foreach ($data['providers'] as $i => $p) {
            $out['providers'][] = $this->parseProvider($p, $i);
        }

        foreach ($data['models'] as $i => $m) {
            $out['models'][] = $this->parseModel($m, $i);
        }

        foreach ($data['prices'] as $i => $pr) {
            $out['prices'][] = $this->parsePrice($pr, $i, $out['providers']);
        }

        return $out;
    }

    /**
     * @param mixed $p
     * @return array{code: string, name: string, offPeak: null|array{window_start: string, window_end: string, multiplier: string}}
     *
     * @throws ImportException
     */
    private function parseProvider(mixed $p, int $i): array
    {
        if (!is_array($p)) {
            throw ImportException::jsonFormat(sprintf('providers[%d] — объект', $i));
        }

        $code = $p['code'] ?? null;
        if (!is_string($code) || $code === '') {
            throw ImportException::jsonFormat(sprintf('providers[%d].code — обязательная строка', $i));
        }
        $name = $p['name'] ?? null;
        if (!is_string($name) || $name === '') {
            throw ImportException::jsonFormat(sprintf('providers[%d].name — обязательная строка', $i));
        }

        return [
            'code' => $code,
            'name' => $name,
            'offPeak' => $this->parseOffPeak($p['off_peak'] ?? null, $i),
        ];
    }

    /**
     * @return array{provider: string, code: string}
     *
     * @throws ImportException
     */
    private function parseModel(mixed $m, int $i): array
    {
        if (!is_array($m)) {
            throw ImportException::jsonFormat(sprintf('models[%d] — объект', $i));
        }

        $provider = $m['provider'] ?? null;
        $code = $m['code'] ?? null;
        if (!is_string($provider) || $provider === '') {
            throw ImportException::jsonFormat(sprintf('models[%d].provider — обязательная строка', $i));
        }
        if (!is_string($code) || $code === '') {
            throw ImportException::jsonFormat(sprintf('models[%d].code — обязательная строка', $i));
        }

        return ['provider' => $provider, 'code' => $code];
    }

    /**
     * @return array{provider: string, model: string, valid_from: string, currency: string, unit: string, rates: array<string, string>}
     *
     * @param array<string, mixed> $provides
     *
     * @throws ImportException
     */
    private function parsePrice(mixed $pr, int $i, array $provides): array
    {
        if (!is_array($pr)) {
            throw ImportException::jsonFormat(sprintf('prices[%d] — объект', $i));
        }

        $provider = $pr['provider'] ?? null;
        if (!is_string($provider) || $provider === '') {
            throw ImportException::jsonFormat(sprintf('prices[%d].provider — обязательная строка', $i));
        }
        $model = $pr['model'] ?? null;
        if (!is_string($model) || $model === '') {
            throw ImportException::jsonFormat(sprintf('prices[%d].model — обязательная строка', $i));
        }

        $validFrom = $pr['valid_from'] ?? null;
        if (!is_string($validFrom) || !self::isValidIso8601Datetime($validFrom)) {
            throw ImportException::jsonFormat(sprintf('prices[%d].valid_from — ISO-8601 UTC (YYYY-MM-DDTHH:MM:SSZ)', $i));
        }

        $unit = $pr['unit'] ?? null;
        if (!is_string($unit)) {
            throw ImportException::jsonFormat(sprintf('prices[%d].unit — обязательная строка', $i));
        }
        if (!in_array($unit, ['per_1m_tokens', 'per_1m_tokens_unified'], true)) {
            throw ImportException::jsonFormat(sprintf(
                'prices[%d].unit — per_1m_tokens|per_1m_tokens_unified (получено "%s")',
                $i,
                $unit,
            ));
        }

        $currency = $pr['currency'] ?? 'USD';
        if (!is_string($currency) || $currency === '') {
            throw ImportException::jsonFormat(sprintf('prices[%d].currency — строка', $i));
        }

        $rates = $pr['rates'] ?? null;
        if (!is_array($rates)) {
            throw ImportException::jsonFormat(sprintf('prices[%d].rates — объект', $i));
        }
        if ($unit === 'per_1m_tokens') {
            $required = ['input', 'output', 'cache_read', 'cache_write'];
        } else {
            $required = ['any'];
        }
        $ratesArr = [];
        foreach ($required as $k) {
            if (!isset($rates[$k])) {
                throw ImportException::jsonFormat(sprintf('prices[%d].rates.%s — обязательный ключ', $i, $k));
            }
            $v = $rates[$k];
            if (!is_scalar($v)) {
                throw ImportException::jsonFormat(sprintf('prices[%d].rates.%s — строка', $i, $k));
            }
            $s = (string) $v;
            if (!preg_match('/^\d+(\.\d{1,6})?$/', $s)) {
                throw ImportException::jsonFormat(sprintf(
                    'prices[%d].rates.%s — десятичная строка с ≤ 6 знаками (получено "%s")',
                    $i,
                    $k,
                    $s,
                ));
            }
            $ratesArr[$k] = $s;
        }

        return [
            'provider' => $provider,
            'model' => $model,
            'valid_from' => $validFrom,
            'currency' => $currency,
            'unit' => $unit,
            'rates' => $ratesArr,
        ];
    }

    /**
     * @return null|array{window_start: string, window_end: string, multiplier: string}
     *
     * @throws ImportException
     */
    private function parseOffPeak(mixed $offPeak, int $i): ?array
    {
        if ($offPeak === null) {
            return null;
        }
        if (!is_array($offPeak)) {
            throw ImportException::jsonFormat(sprintf('providers[%d].off_peak — object|null', $i));
        }

        $ws = $offPeak['window_start'] ?? null;
        $we = $offPeak['window_end'] ?? null;
        $mult = $offPeak['multiplier'] ?? null;

        foreach (['window_start' => $ws, 'window_end' => $we, 'multiplier' => $mult] as $k => $v) {
            if (!is_string($v) || $v === '') {
                throw ImportException::jsonFormat(sprintf('providers[%d].off_peak.%s — обязательная строка', $i, $k));
            }
        }
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', (string) $ws)) {
            throw ImportException::jsonFormat(sprintf('providers[%d].off_peak.window_start — HH:MM', $i));
        }
        if (!preg_match('/^([01]\d|2[0-3]):[0-5]\d$/', (string) $we)) {
            throw ImportException::jsonFormat(sprintf('providers[%d].off_peak.window_end — HH:MM', $i));
        }
        if ((string) $ws === (string) $we) {
            throw ImportException::jsonFormat(sprintf('providers[%d].off_peak: window_start = window_end запрещено', $i));
        }
        if (!preg_match('/^\d+(\.\d{1,6})?$/', (string) $mult)) {
            throw ImportException::jsonFormat(sprintf('providers[%d].off_peak.multiplier — десятичная строка', $i));
        }

        return [
            'window_start' => $ws,
            'window_end' => $we,
            'multiplier' => $mult,
        ];
    }

    private static function isValidIso8601Datetime(string $s): bool
    {
        if (
            !preg_match('/^(\d{4})-(\d{2})-(\d{2})T(\d{2}):(\d{2}):(\d{2})(?:\.\d+)?(Z|[+-]\d{2}:\d{2})?$/', $s, $m)
        ) {
            return false;
        }

        return \DateTimeImmutable::createFromFormat(\DateTimeInterface::ATOM, $s) !== false;
    }
}
