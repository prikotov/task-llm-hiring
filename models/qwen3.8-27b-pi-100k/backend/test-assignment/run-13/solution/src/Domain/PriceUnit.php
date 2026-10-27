<?php

declare(strict_types=1);

namespace App\Domain;

final class PriceUnit
{
    public const PER_1M_TOKENS = 'per_1m_tokens';
    public const PER_1M_TOKENS_UNIFIED = 'per_1m_tokens_unified';

    /** Все виды токенов в схеме per_1m_tokens. */
    public const TOKEN_KINDS = ['input', 'output', 'cache_read', 'cache_write'];

    /**
     * Ключи ставок для каждой схемы.
     *
     * @return array<string, list<string>>
     */
    public static function schemaRates(string $unit): array
    {
        return match ($unit) {
            self::PER_1M_TOKENS => self::TOKEN_KINDS,
            self::PER_1M_TOKENS_UNIFIED => ['any'],
            default => throw new PricingSchemaException(sprintf('Неизвестная схема тарификации: "%s"', $unit)),
        };
    }

    /**
     * Стоимость события: Σ tokens × rate / 1_000_000.
     *
     * @param array<string, string> $rates ключи — имена ставок
     */
    public static function cost(
        string $unit,
        array $rates,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ): string {
        foreach (self::schemaRates($unit) as $key) {
            if (! array_key_exists($key, $rates)) {
                throw new PricingSchemaException(sprintf('Отсутствует ставка "%s" для схемы "%s"', $key, $unit));
            }
        }

        if ($unit === self::PER_1M_TOKENS_UNIFIED) {
            return self::rateCost(
                $tokensInput + $tokensOutput + $tokensCacheRead + $tokensCacheWrite,
                $rates['any'],
            );
        }

        $cost = '0';
        foreach (self::TOKEN_KINDS as $kind) {
            $tokens = match ($kind) {
                'input' => $tokensInput,
                'output' => $tokensOutput,
                'cache_read' => $tokensCacheRead,
                'cache_write' => $tokensCacheWrite,
            };
            $cost = Money::add($cost, self::rateCost($tokens, $rates[$kind]));
        }

        return $cost;
    }

    public static function rateCost(int $tokens, string $rate): string
    {
        return Money::div(Money::mul((string) $tokens, $rate), '1000000');
    }

    /**
     * Нормализуем ставки (валидация + стринговые значения).
     *
     * @param array<string, mixed> $rates
     *
     * @return array<string, string>
     */
    public static function normalizeRates(string $unit, array $rates): array
    {
        $expected = self::schemaRates($unit);
        $normalized = [];
        foreach ($expected as $key) {
            if (! array_key_exists($key, $rates)) {
                throw new PricingSchemaException(sprintf('Отсутствует ставка "%s" для схемы "%s"', $key, $unit));
            }
            $normalized[$key] = Money::format((string) $rates[$key]);
        }

        return $normalized;
    }

    private function __construct()
    {
    }
}
