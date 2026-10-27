<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Usage\TokenBreakdown;

/**
 * Строка цен модели: ставки + схема тарификации, действующая с validFrom.
 */
final readonly class PricedModel
{
    /**
     * @param array<string, string> $rates ставки как десятичные строки
     */
    public function __construct(
        public PricingScheme $scheme,
        public array $rates,
    ) {
        foreach ($scheme->expectedRateKeys() as $key) {
            if (!isset($rates[$key]) || !preg_match('/^\d+(\.\d+)?$/', (string) $rates[$key])) {
                throw new \InvalidArgumentException(
                    sprintf('Rate "%s" is missing or not a decimal string.', $key)
                );
            }
        }
    }

    /**
     * Точная стоимость расщепления токенов по текущей строке цен (без округления).
     */
    public function costFor(TokenBreakdown $tokens): Money
    {
        return match ($this->scheme) {
            PricingScheme::PerToken => $tokens->tokenInput()
                ->multiplyBy($this->rates['input'])
                ->add($tokens->tokenOutput()->multiplyBy($this->rates['output']))
                ->add($tokens->tokenCacheRead()->multiplyBy($this->rates['cache_read']))
                ->add($tokens->tokenCacheWrite()->multiplyBy($this->rates['cache_write']))
                ->divideBy(Money::UNIT_TOKENS),
            PricingScheme::Unified => $tokens->total()
                ->multiplyBy($this->rates['any'])
                ->divideBy(Money::UNIT_TOKENS),
        };
    }
}
