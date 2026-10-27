<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

final class PriceRow
{
    public function __construct(
        public readonly string $providerCode,
        public readonly string $modelCode,
        public readonly \DateTimeImmutable $validFrom,
        public readonly string $currency,
        public readonly RateSpec $spec,
    ) {
        if ($this->providerCode === '' || $this->modelCode === '') {
            throw new \InvalidArgumentException('Provider and model codes must be non-empty.');
        }
        if (strlen($this->currency) !== 3) {
            throw new \InvalidArgumentException('Currency must be exactly 3 characters.');
        }
    }

    public function rate(string $kind): string
    {
        return $this->spec->rate($kind);
    }

    public function isUnified(): bool
    {
        return $this->spec->isUnified();
    }

    public function spec(): RateSpec
    {
        return $this->spec;
    }
}
