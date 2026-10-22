<?php

declare(strict_types=1);

namespace App\Domain\Usage;

/**
 * Вид токенов (соответствует ключам rates в схеме per_1m_tokens
 * и полям событий использования).
 */
enum TokenKind: string
{
    case Input = 'input';
    case Output = 'output';
    case CacheRead = 'cache_read';
    case CacheWrite = 'cache_write';

    public function tokenField(): string
    {
        return 'tokens_'.$this->value;
    }

    /**
     * @return list<TokenKind>
     */
    public static function all(): array
    {
        return self::cases();
    }
}
