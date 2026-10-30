<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

enum TokenKind: string
{
    case Input = 'input';
    case Output = 'output';
    case CacheRead = 'cache_read';
    case CacheWrite = 'cache_write';
    case Any = 'any';

    /** Виды токенов, которые суммируются в потреблении события. */
    public static function billable(): array
    {
        return [self::Input, self::Output, self::CacheRead, self::CacheWrite];
    }
}
