<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

final class PriceUnit
{
    public const PER_1M_TOKENS = 'per_1m_tokens';
    public const PER_1M_TOKENS_UNIFIED = 'per_1m_tokens_unified';

    public const ALL = [self::PER_1M_TOKENS, self::PER_1M_TOKENS_UNIFIED];

    private function __construct()
    {
    }
}
