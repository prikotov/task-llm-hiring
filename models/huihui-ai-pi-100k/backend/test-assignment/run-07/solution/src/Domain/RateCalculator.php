<?php

declare(strict_types=1);

namespace App\Domain;

/**
 * Расчёт точной (неокруглённой) стоимости события.
 *
 * Схемы тарификации:
 *  - `per_1m_tokens`: cost = Σ tokens_вид × rate_вид / 1_000_000;
 *  - `per_1m_tokens_unified`: cost = (Σ tokens) × rate_any / 1_000_000.
 *
 * Если событие попадает в оф-пик провайдера — стоимость всего события
 * умножается на `multiplier`. Итоговая стоимость события затем округляется
 * до 6 знаков HALF_UP (Money::halfUp6).
 */
final class RateCalculator
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    /**
     * @param array{unit: string, rates: array<string, string>} $price
     * @param array{
     *     input: int,
     *     output: int,
     *     cache_read: int,
     *     cache_write: int
     * } $tokens
     */
    public function costUsd(array $price, array $tokens, ?OffPeak $offPeak, \DateTimeInterface $tsUtc): string
    {
        $base = match ($price['unit']) {
            self::UNIT_PER_TOKEN => $this->perToken($price['rates'], $tokens),
            self::UNIT_UNIFIED => $this->unified($price['rates'], $tokens),
            default => throw new \LogicException(sprintf('Неизвестная схема тарификации "%s"', $price['unit'])),
        };

        $cost = $base;
        if ($offPeak !== null && $offPeak->appliesAtUtc(IsoUtc::toUtc($tsUtc))) {
            $cost = Money::mul($cost, $offPeak->multiplier());
        }

        return Money::halfUp6($cost);
    }

    /**
     * @param array<string, string> $rates
     */
    private function perToken(array $rates, array $tokens): string
    {
        $sum = '0';
        foreach (['input', 'output', 'cache_read', 'cache_write'] as $kind) {
            if (!isset($rates[$kind])) {
                throw new \LogicException(sprintf('Схема per_1m_tokens: отсутствует ставка "%s"', $kind));
            }

            $sum = Money::add($sum, self::part((int) $tokens[$kind], (string) $rates[$kind]));
        }

        return $sum;
    }

    /**
     * @param array<string, string> $rates
     * @param array{input: int, output: int, cache_read: int, cache_write: int} $tokens
     */
    private function unified(array $rates, array $tokens): string
    {
        if (!isset($rates['any'])) {
            throw new \LogicException('Схема per_1m_tokens_unified: отсутствует ставка "any"');
        }

        $total = ((int) $tokens['input'])
            + ((int) $tokens['output'])
            + ((int) $tokens['cache_read'])
            + ((int) $tokens['cache_write']);

        return self::part($total, (string) $rates['any']);
    }

    /**
     * tokens × rate / 1_000_000, без промежуточного округления.
     */
    private static function part(int $tokens, string $rate): string
    {
        if ($tokens <= 0) {
            return '0';
        }

        return Money::div(Money::mul((string) $tokens, $rate), '1000000');
    }
}
