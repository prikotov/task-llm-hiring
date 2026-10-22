<?php

declare(strict_types=1);

namespace App\Infrastructure;

/**
 * Парсер и валидатор истории цен (JSON).
 *
 * Возвращает уже нормализованные данные для дальнейшего загрузки в БД.
 */
final class PriceParser
{
    public const UNIT_PER_1M = 'per_1m_tokens';
    public const UNIT_PER_1M_UNIFIED = 'per_1m_tokens_unified';

    /**
     * @param string $content содержимое JSON-файла
     * @return array{
     *     providers: array<int,array{code:string,name:string,off_peak_start(?string),off_peak_end(?string),off_peak_multiplier(?string)}[]>,
     *     models: array<int,array{provider:string,code:string}>[],
     *     prices: array<int,array{provider:string,model:string,valid_from:string,currency:string,unit:string,rates:array<string,string>}>[]
     * }
     */
    public function parse(string $content): array
    {
        $raw = json_decode($content, true, 512, JSON_THROW_ON_ERROR);

        if (!is_array($raw)) {
            throw new \RuntimeException('История цен: ожидаемый JSON-объект.');
        }

        $providers = $this->parseProviders($raw);
        $models = $this->parseModels($raw);
        $priceRows = $this->parsePrices($raw);

        return [
            'providers' => $providers,
            'models' => $models,
            'prices' => $priceRows,
        ];
    }

    /**
     * @return array<int,array{code:string,name:string,off_peak_start(?string),off_peak_end(?string),off_peak_multiplier(?string)}[]>
     */
    private function parseProviders(array $raw): array
    {
        $providers = [];

        if (!isset($raw['providers']) || !is_array($raw['providers'])) {
            return $providers;
        }

        foreach ($raw['providers'] as $provider) {
            if (!is_array($provider)
                || !isset($provider['code'])
                || !is_string($provider['code'])
            ) {
                throw new \RuntimeException(
                    'История цен: провайдер должен иметь строковый "code".',
                );
            }

            if (isset($provider['name']) && !is_string($provider['name'])) {
                throw new \RuntimeException(
                    'История цен: провайдер должен иметь строковый "name".',
                );
            }

            [$start, $end, $multiplier] = $this->normalizeOffPeak(
                $provider['off_peak'] ?? null,
            );

            $providers[] = [
                'code' => $provider['code'],
                'name' => $provider['name'] ?? $provider['code'],
                'off_peak_start' => $start,
                'off_peak_end' => $end,
                'off_peak_multiplier' => $multiplier,
            ];
        }

        return $providers;
    }

    /**
     * @return array{0:?string,1:?string,2:?string}
     */
    private function normalizeOffPeak(mixed $offPeak): array
    {
        if ($offPeak === null) {
            return [null, null, null];
        }

        if (!is_array($offPeak)) {
            throw new \RuntimeException(
                'История цен: off_peak должен быть объектом или null.',
            );
        }

        if (isset($offPeak['window_start'], $offPeak['window_end'])) {
            $start = $this->validateTime($offPeak['window_start']);
            $end = $this->validateTime($offPeak['window_end']);
        } else {
            $start = null;
            $end = null;
        }

        $multiplier = $offPeak['multiplier'] ?? null;
        $multiplier = ($multiplier !== null) ? (string) $multiplier : null;

        $hasAll = ($start !== null && $end !== null && $multiplier !== null);
        $hasNone = ($start === null && $end === null && $multiplier === null);

        if ($start !== null || $end !== null || $multiplier !== null) {
            if (!$hasAll) {
                throw new \RuntimeException(
                    'История цен: off_peak должен содержать все поля или быть null.',
                );
            }
        } elseif (!$hasNone) {
            throw new \RuntimeException(
                'История цен: off_peak должен содержать все поля или be null.',
            );
        }

        return [$start, $end, $multiplier];
    }

    private function validateTime(mixed $value): ?string
    {
        if (($value === null) || !is_string($value)) {
            return null;
        }

        if (!preg_match('/^([0-1][0-9]|2[0-3]):([0-5][0-9])$/', $value, $m)) {
            throw new \RuntimeException(
                'История цен: off_peak окно задано в неверном формате ЧЧ:ММ.',
            );
        }

        return $value;
    }

    /**
     * @return array<int,array{provider:string,code:string}>
     */
    private function parseModels(array $raw): array
    {
        $models = [];

        if (!isset($raw['models']) || !is_array($raw['models'])) {
            return $models;
        }

        foreach ($raw['models'] as $model) {
            if (!is_array($model)
                || !isset($model['provider']) || !is_string($model['provider'])
                || !isset($model['code']) || !is_string($model['code'])
            ) {
                throw new \RuntimeException(
                    'История цен: модель должна иметь "provider" и "code".',
                );
            }

            $models[] = [
                'provider' => $model['provider'],
                'code' => $model['code'],
            ];
        }

        return $models;
    }

    /**
     * @return array<int,array{provider:string,model:string,valid_from:string,currency:string,unit:string,rates:array<string,string>}>
     */
    private function parsePrices(array $raw): array
    {
        $units = [self::UNIT_PER_1M, self::UNIT_PER_1M_UNIFIED];

        $prices = [];

        if (!isset($raw['prices']) || !is_array($raw['prices'])) {
            return $prices;
        }

        foreach ($raw['prices'] as $price) {
            if (!is_array($price)) {
                throw new \RuntimeException(
                    'История цены: строка цены должна быть объектом.',
                );
            }

            foreach (['provider', 'model', 'valid_from', 'currency', 'unit'] as $field) {
                if (!isset($price[$field]) || !is_string($price[$field])) {
                    throw new \RuntimeException(
                        sprintf('История цены: ставка должна иметь "%s".', $field),
                    );
                }
            }

            if (!in_array($price['unit'], $units, true)) {
                throw new \RuntimeException(
                    'История цены: неизвестная схема тарификации "' . $price['unit'] . '".',
                );
            }

            if (!isset($price['rates']) || !is_array($price['rates'])) {
                throw new \RuntimeException(
                    'История цены: ставка должна иметь "rates".',
                );
            }

            foreach ($price['rates'] as $rate) {
                if (!is_string($rate)) {
                    throw new \RuntimeException(
                        'История цены: ставка должна быть десятичной строкой.',
                    );
                }

                if (!preg_match('/^-?\d+(\.\d+)?$/', $rate)) {
                    throw new \RuntimeException(
                        'История цены: ставка должна быть десятичным числом.',
                    );
                }
            }

            $prices[] = [
                'provider' => $price['provider'],
                'model' => $price['model'],
                'valid_from' => $price['valid_from'],
                'currency' => $price['currency'],
                'unit' => $price['unit'],
                'rates' => $price['rates'],
            ];
        }

        return $prices;
    }
}
