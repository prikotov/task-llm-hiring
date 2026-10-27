<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

/**
 * Lightweight reference to a provider for book-level lookups.
 */
final class ProviderRef
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?OffPeakWindow $offPeak,
    ) {
    }
}
