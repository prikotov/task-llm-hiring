<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Billing\Cost;
use App\Domain\Billing\TokenMix;

/**
 * Схема тарификации провайдера.
 *
 * per_1m_tokens          — цена за 1 000 000 токенов отдельно для каждого вида;
 * per_1m_tokens_unified  — единая цена за 1 000 000 токенов любого вида.
 */
enum BillingUnit: string
{
    case PER_1M_TOKENS = 'per_1m_tokens';
    case PER_1M_TOKENS_UNIFIED = 'per_1m_tokens_unified';

    /**
     * Вычисляет точную (без округления) стоимость потребления по данной схеме.
     *
     * @param array<string, Cost> $rates
     */
    public function calculateCost(TokenMix $tokens, array $rates): Cost
    {
        return match ($this) {
            self::PER_1M_TOKENS => $this->perTokenCost($tokens, $rates),
            self::PER_1M_TOKENS_UNIFIED => Cost::sum([
                $this->ratePart($rates['any'] ?? throw new \LogicException('unified rate "any" is missing'), $tokens->total()),
            ]),
        };
    }

    private function perTokenCost(TokenMix $tokens, array $rates): Cost
    {
        return Cost::sum([
            $this->ratePart($rates['input'] ?? throw new \LogicException('rate "input" is missing'), $tokens->input),
            $this->ratePart($rates['output'] ?? throw new \LogicException('rate "output" is missing'), $tokens->output),
            $this->ratePart($rates['cache_read'] ?? throw new \LogicException('rate "cache_read" is missing'), $tokens->cacheRead),
            $this->ratePart($rates['cache_write'] ?? throw new \LogicException('rate "cache_write" is missing'), $tokens->cacheWrite),
        ]);
    }

    private function ratePart(Cost $ratePerMillion, int $tokenCount): Cost
    {
        if ($tokenCount === 0) {
            return Cost::zero();
        }

        // tokens × rate / 1 000 000 — точное десятичное деление.
        return Cost::make(bcdiv(bcmul($ratePerMillion->toSixDecimalsTruncated(), (string) $tokenCount, 20), '1000000', 20));
    }
}
