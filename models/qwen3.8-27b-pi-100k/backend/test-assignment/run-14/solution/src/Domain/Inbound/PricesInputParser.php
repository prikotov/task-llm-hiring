<?php

declare(strict_types=1);

namespace App\Domain\Inbound;

use App\Domain\Pricing\OffPeakWindow;
use App\Entity\Provider;
use App\Entity\Price;
use App\Exception\InvalidInputException;

/**
 * Парсер истории цен (JSON). Возвращает плоскую структуру, готовую
 * для записи в БД; валидировашиеся бизнес-правила — сюда же.
 */
final readonly class PricesInputParser
{
    /**
     * Разбирает JSON-строку.
     *
     * @return array{providers: array, models: array, prices: array}
     * @throws InvalidInputException
     */
    /**
     * @param bool $validateRefs кросс-валидация цен по моделям и провайдерам файла
     *   (для app:billing:reprice — false, модель/провайдер ищутся в БД).
     */
    public static function parse(string $json, bool $validateRefs = true): array
    {
        $decoded = json_decode($json, true);
        if (!is_array($decoded)) {
            throw new InvalidInputException('prices.json: root must be a JSON object.');
        }

        $providers = self::parseProviders($decoded['providers'] ?? []);
        $models = self::parseModels($decoded['models'] ?? []);
        $prices = self::parsePrices($decoded['prices'] ?? []);

        if ($validateRefs) {
            // Кросс-валидация: все модели и цены должны ссылаться на известные провайдеры и модели.
            $providerCodes = array_map(fn (array $p) => $p['code'], $providers);
            foreach ($models as $m) {
                if (!in_array($m['provider'], $providerCodes, true)) {
                    throw new InvalidInputException(sprintf('prices.json: model references unknown provider "%s".', $m['provider']));
                }
            }

            $knownModelPairs = array_map(
                static fn (array $m) => $m['provider'].'|'.$m['code'],
                $models
            );
            foreach ($prices as $p) {
                if (!in_array($p['provider'].'|'.$p['model'], $knownModelPairs, true)) {
                    throw new InvalidInputException(sprintf('prices.json: price references unknown model "%s/%s".', $p['provider'], $p['model']));
                }
            }
        }

        return ['providers' => $providers, 'models' => $models, 'prices' => $prices];
    }

    private static function parseProviders(mixed $raw): array
    {
        if (!is_array($raw)) {
            throw new InvalidInputException('prices.json: "providers" must be an array.');
        }

        $seenCodes = [];
        $result = [];
        foreach ($raw as $item) {
            if (!is_array($item)) {
                throw new InvalidInputException('prices.json: provider entry must be an object.');
            }
            $code = self::string($item['code'] ?? null, 'provider.code');
            if (in_array($code, $seenCodes, true)) {
                throw new InvalidInputException(sprintf('prices.json: duplicate provider code "%s".', $code));
            }
            $seenCodes[] = $code;

            $name = self::string($item['name'] ?? null, 'provider.name');

            $offPeak = $item['off_peak'] ?? null;
            if ($offPeak === null || $offPeak === []) {
                $result[] = ['code' => $code, 'name' => $name, 'off_peak' => null];
                continue;
            }

            // Ограничиваем строку на 16 символов для краткости, не важно
            $result[] = [
                'code' => $code,
                'name' => $name,
                'off_peak' => self::parseOffPeak($offPeak),
            ];
        }

        return $result;
    }

    private static function parseOffPeak(mixed $raw): ?OffPeakWindow
    {
        if (!is_array($raw)) {
            throw new InvalidInputException('provider.off_peak: must be an object.');
        }
        $start = self::string($raw['window_start'] ?? null, 'off_peak.window_start');
        $end = self::string($raw['window_end'] ?? null, 'off_peak.window_end');
        $mult = self::string($raw['multiplier'] ?? null, 'off_peak.multiplier');

        if (!preg_match('/^\d{2}:\d{2}$/', $start)) {
            throw new InvalidInputException('off_peak.window_start must be HH:MM.');
        }
        if (!preg_match('/^\d{2}:\d{2}$/', $end)) {
            throw new InvalidInputException('off_peak.window_end must be HH:MM.');
        }

        // multiplier — десятичная строка, > 0
        if (!preg_match('/^(0|0+([1-9]\d*))(?:\.\d+)?$/', $mult) && !preg_match('/^\d+(\.\d+)?$/', $mult)) {
            throw new InvalidInputException('off_peak.multiplier must be a decimal string > 0.');
        }
        if (bccomp($mult, '0', 6) <= 0) {
            throw new InvalidInputException('off_peak.multiplier must be positive.');
        }

        return new OffPeakWindow($start, $end, $mult);
    }

    private static function parseModels(mixed $raw): array
    {
        if (!is_array($raw)) {
            throw new InvalidInputException('prices.json: "models" must be an array.');
        }
        $seen = [];
        $result = [];
        foreach ($raw as $item) {
            if (!is_array($item)) {
                throw new InvalidInputException('prices.json: model entry must be an object.');
            }
            $provider = self::string($item['provider'] ?? null, 'model.provider');
            $code = self::string($item['code'] ?? null, 'model.code');
            $pair = $provider.'|'.$code;
            if (in_array($pair, $seen, true)) {
                throw new InvalidInputException(sprintf('prices.json: duplicate model "%s".', $pair));
            }
            $seen[] = $pair;
            $result[] = ['provider' => $provider, 'code' => $code];
        }

        return $result;
    }

    private static function parsePrices(mixed $raw): array
    {
        if (!is_array($raw)) {
            throw new InvalidInputException('prices.json: "prices" must be an array.');
        }
        $seen = [];
        $result = [];
        foreach ($raw as $item) {
            if (!is_array($item)) {
                throw new InvalidInputException('prices.json: price entry must be an object.');
            }
            $provider = self::string($item['provider'] ?? null, 'price.provider');
            $model = self::string($item['model'] ?? null, 'price.model');
            $validFrom = self::parseTs($item['valid_from'] ?? null, 'valid_from');
            $currency = self::string($item['currency'] ?? 'USD', 'price.currency');
            $unit = self::string($item['unit'] ?? null, 'price.unit');
            $rates = $item['rates'] ?? null;
            if (!is_array($rates)) {
                throw new InvalidInputException('price.rates: must be an object.');
            }

            $key = sprintf('%s|%s|%s', $provider, $model, $validFrom->getTimestamp());
            if (in_array($key, $seen, true)) {
                throw new InvalidInputException(sprintf('prices.json: duplicate price (%s).', str_replace('|', ' ', $key)));
            }
            $seen[] = $key;

            // Схема ставок — зависит от unit.
            if ($unit === 'per_1m_tokens') {
                foreach (['input', 'output', 'cache_read', 'cache_write'] as $k) {
                    self::string($rates[$k] ?? null, "price.rates.$k");
                }
            } elseif ($unit === 'per_1m_tokens_unified') {
                self::string($rates['any'] ?? null, 'price.rates.any');
            } else {
                throw new InvalidInputException(sprintf('price.unit "%s" is unknown.', $unit));
            }

            $result[] = [
                'provider' => $provider,
                'model' => $model,
                'valid_from' => $validFrom,
                'currency' => $currency,
                'unit' => $unit,
                'rates' => $rates,
            ];
        }

        return $result;
    }

    private static function string(mixed $value, string $label): string
    {
        if (!is_string($value) || $value === '') {
            throw new InvalidInputException(sprintf('%s: must be a non-empty string.', $label));
        }

        return $value;
    }

    private static function parseTs(mixed $value, string $label): \DateTimeImmutable
    {
        if (!is_string($value)) {
            throw new InvalidInputException(sprintf('%s: must be an ISO-8601 string.', $label));
        }
        if (!str_ends_with($value, 'Z')) {
            throw new InvalidInputException(sprintf('%s: must end with "Z" (UTC).', $label));
        }
        try {
            $dt = new \DateTimeImmutable($value, new \DateTimeZone('UTC'));
        } catch (\Exception) {
            throw new InvalidInputException(sprintf('%s: invalid ISO-8601 "%s".', $label, $value));
        }

        return $dt->setTimezone(new \DateTimeZone('UTC'));
    }
}
