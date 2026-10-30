<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

enum PriceUnit: string
{
    case PerMillionTokens = 'per_1m_tokens';
    case PerMillionTokensUnified = 'per_1m_tokens_unified';
}
