<?php

declare(strict_types=1);

namespace App\Domain;

use DomainException;

/**
 * Расчёт точной (не округлённой) стоимости одного события.
 *
 * per_1m_tokens:        Σ tokens_вид × rate_вид / 1 000 000
 * per_1m_tokens_unified: (Σ все токен-виды) × rate_any / 1 000 000
 * Оф-пик (если UTC-момент в окне провайдера): итог × multiplier.
 *
 * Округление — НЕ здесь: вызывающий применяет ровно одно roundHalfUp().
 */
final class BillingComputer
{
    public function calculate(
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        PriceUnit $unit,
        array $rates,
        ?OffPeakWindow $offPeak,
        \DateTimeInterface $tsUtc,
    ): Money {
        $base = match ($unit) {
            PriceUnit::Per1mTokens => $this->sumByTokenKind(
                $tokensInput,
                $tokensOutput,
                $tokensCacheRead,
                $tokensCacheWrite,
                $rates,
            ),
            PriceUnit::Per1mTokensUnified => Money::fromDecimal((string) (
                $tokensInput + $tokensOutput + $tokensCacheRead + $tokensCacheWrite
            ))
                ->multiplyBy(self::requireRate($rates, 'any'))
                ->perMillion(),
        };

        if ($offPeak !== null && $offPeak->appliesTo($tsUtc)) {
            $base = $base->multiplyBy($offPeak->multiplier());
        }

        return $base;
    }

    private function sumByTokenKind(
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        array $rates,
    ): Money {
        $kinds = [
            'input' => $tokensInput,
            'output' => $tokensOutput,
            'cache_read' => $tokensCacheRead,
            'cache_write' => $tokensCacheWrite,
        ];

        $sum = Money::zero();
        foreach ($kinds as $kind => $tokens) {
            $term = Money::fromDecimal((string) $tokens)
                ->multiplyBy(self::requireRate($rates, $kind))
                ->perMillion();
            $sum = $sum->add($term);
        }

        return $sum;
    }

    private static function requireRate(array $rates, string $key): string
    {
        if (!array_key_exists($key, $rates) || !is_string($rates[$key])) {
            throw new DomainException("В строке цен отсутствует ставка '{$key}'");
        }

        return $rates[$key];
    }
}
