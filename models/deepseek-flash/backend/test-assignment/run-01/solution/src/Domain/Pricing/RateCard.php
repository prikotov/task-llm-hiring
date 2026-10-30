<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Money\Decimal;
use App\Domain\Usage\TokenUsage;
use InvalidArgumentException;

/**
 * Rates of one price-list row, independent of its validity period.
 */
final readonly class RateCard
{
    private const int TOKENS_PER_MILLION = 1_000_000;

    private function __construct(
        public PriceUnit $unit,
        public ?Decimal $input,
        public ?Decimal $output,
        public ?Decimal $cacheRead,
        public ?Decimal $cacheWrite,
        public ?Decimal $any,
    ) {
    }

    public static function perToken(
        Decimal $input,
        Decimal $output,
        Decimal $cacheRead,
        Decimal $cacheWrite,
    ): self {
        return new self(PriceUnit::PerMillionTokens, $input, $output, $cacheRead, $cacheWrite, null);
    }

    public static function unified(Decimal $any): self
    {
        return new self(PriceUnit::PerMillionTokensUnified, null, null, null, null, $any);
    }

    public function baseCost(TokenUsage $usage): Decimal
    {
        $million = Decimal::fromInt(self::TOKENS_PER_MILLION);

        return match ($this->unit) {
            PriceUnit::PerMillionTokens => Decimal::fromInt($usage->input)
                ->multiply($this->requireRate($this->input))
                ->add(Decimal::fromInt($usage->output)->multiply($this->requireRate($this->output)))
                ->add(Decimal::fromInt($usage->cacheRead)->multiply($this->requireRate($this->cacheRead)))
                ->add(Decimal::fromInt($usage->cacheWrite)->multiply($this->requireRate($this->cacheWrite)))
                ->divide($million),
            PriceUnit::PerMillionTokensUnified => $usage->total()
                ->multiply($this->requireRate($this->any))
                ->divide($million),
        };
    }

    private function requireRate(?Decimal $rate): Decimal
    {
        if ($rate === null) {
            throw new InvalidArgumentException(sprintf('Rate is missing for pricing unit "%s".', $this->unit->value));
        }

        return $rate;
    }
}
