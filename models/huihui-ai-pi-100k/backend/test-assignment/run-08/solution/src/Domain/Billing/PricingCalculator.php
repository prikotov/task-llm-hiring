<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use DateTimeImmutable;

/**
 * Расчёт стоимости одного события по строке цен (PriceRow)
 * и условиям оф-пика провайдера (OffPeak, если есть).
 *
 * Правила:
 *  - per_1m_tokens:        cost = Σ tokens_kind × rate_kind / 1e6;
 *  - per_1m_tokens_unified: cost = (Σ tokens) × rate_any / 1e6;
 *  - оф-пик: множитель применяется ко всей стоимости события;
 *  - ровно одно округление (HALF_UP до 6 знаков) — на итог события.
 */
final class PricingCalculator
{
    public const UNIT_PER_1M = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    private const PER_1M_TOKEN_KINDS = ['input', 'output', 'cache_read', 'cache_write'];
    private const TOKENS_DIVISOR = '1000000';

    /**
     * @param array{input:int,output:int,cache_read:int,cache_write:int} $tokens
     * @param array<string,string> $rates строки-числа (6 знаков)
     */
    public static function computeEventCost(
        array $tokens,
        string $unit,
        array $rates,
        ?OffPeak $offPeak,
        DateTimeImmutable $ts,
    ): string {
        $cost = match ($unit) {
            self::UNIT_PER_1M => self::tokenKindsCost($tokens, $rates),
            self::UNIT_UNIFIED => self::unifiedCost($tokens, $rates['any']),
            default => throw new \DomainException(sprintf('Unknown pricing unit: %s', $unit)),
        };

        if ($offPeak !== null && $offPeak->appliesAt($ts)) {
            $cost = MoneyMath::mul($cost, $offPeak->multiplier);
        }

        return MoneyMath::roundHalfUpTo6($cost);
    }

    /** @param array{input:int,output:int,cache_read:int,cache_write:int} $tokens */
    private static function tokenKindsCost(array $tokens, array $rates): string
    {
        $cost = MoneyMath::zero();

        foreach (self::PER_1M_TOKEN_KINDS as $kind) {
            $part = MoneyMath::div(
                MoneyMath::mul((string) $tokens[$kind], (string) $rates[$kind]),
                self::TOKENS_DIVISOR,
            );
            $cost = MoneyMath::add($cost, $part);
        }

        return $cost;
    }

    /** @param array{input:int,output:int,cache_read:int,cache_write:int} $tokens */
    private static function unifiedCost(array $tokens, string $rate): string
    {
        $totalTokens = array_sum($tokens);

        if ($totalTokens <= 0) {
            return MoneyMath::zero();
        }

        return MoneyMath::div(MoneyMath::mul((string) $totalTokens, $rate), self::TOKENS_DIVISOR);
    }
}
