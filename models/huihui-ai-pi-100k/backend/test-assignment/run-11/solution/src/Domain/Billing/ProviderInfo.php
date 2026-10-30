<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Pricing\OffPeakPolicy;

/**
 * Информация о провайдере, необходимая для тарификации.
 */
final class ProviderInfo
{
    public function __construct(
        public readonly string $code,
        public readonly ?OffPeakPolicy $offPeak = null,
    ) {
    }
}
