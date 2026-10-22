<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

enum TokenKind: string
{
    case Input = 'input';
    case Output = 'output';
    case CacheRead = 'cache_read';
    case CacheWrite = 'cache_write';
}
