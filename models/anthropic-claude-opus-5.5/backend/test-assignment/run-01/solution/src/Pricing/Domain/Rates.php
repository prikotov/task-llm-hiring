<?php

declare(strict_types=1);

namespace App\Pricing\Domain;

use App\Shared\Decimal;
use InvalidArgumentException;

/**
 * Ставки строки цены с учётом схемы тарификации. Считает точную (неокруглённую) стоимость.
 */
final readonly class Rates
{
    private const string MILLION = '1000000';

    /** @param array<string, Decimal> $rates */
    private function __construct(public PricingUnit $unit, public array $rates)
    {
    }

    /** @param array<array-key, mixed> $raw */
    public static function fromArray(PricingUnit $unit, array $raw): self
    {
        $keys = match ($unit) {
            PricingUnit::PerMillionTokens => ['input', 'output', 'cache_read', 'cache_write'],
            PricingUnit::PerMillionTokensUnified => ['any'],
        };
        $rates = [];
        foreach ($keys as $k) {
            if (!isset($raw[$k]) || !is_string($raw[$k])) {
                throw new InvalidArgumentException(sprintf('Ставка "%s" обязательна и должна быть десятичной строкой', $k));
            }
            $rate = Decimal::of($raw[$k]);
            if ($rate->isNegative()) {
                throw new InvalidArgumentException(sprintf('Ставка "%s" не может быть отрицательной', $k));
            }
            $rates[$k] = $rate;
        }

        return new self($unit, $rates);
    }

    public function exactCost(TokenUsage $t): Decimal
    {
        $m = Decimal::of(self::MILLION);

        return match ($this->unit) {
            PricingUnit::PerMillionTokens => Decimal::of($t->input)->mul($this->rates['input'])
                ->add(Decimal::of($t->output)->mul($this->rates['output']))
                ->add(Decimal::of($t->cacheRead)->mul($this->rates['cache_read']))
                ->add(Decimal::of($t->cacheWrite)->mul($this->rates['cache_write']))
                ->div($m),
            PricingUnit::PerMillionTokensUnified => Decimal::of($t->total())->mul($this->rates['any'])->div($m),
        };
    }

    public function equals(self $o): bool
    {
        if ($this->unit !== $o->unit || array_keys($this->rates) !== array_keys($o->rates)) {
            return false;
        }
        foreach ($this->rates as $k => $v) {
            if (!$v->equals($o->rates[$k])) {
                return false;
            }
        }

        return true;
    }

    /** @return array<string, string> */
    public function toArray(): array
    {
        return array_map(static fn (Decimal $d): string => $d->value, $this->rates);
    }
}
