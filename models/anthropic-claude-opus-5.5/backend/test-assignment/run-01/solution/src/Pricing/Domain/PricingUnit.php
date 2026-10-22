<?php

declare(strict_types=1);

namespace App\Pricing\Domain;

enum PricingUnit: string
{
    case PerMillionTokens = 'per_1m_tokens';
    case PerMillionTokensUnified = 'per_1m_tokens_unified';
}
