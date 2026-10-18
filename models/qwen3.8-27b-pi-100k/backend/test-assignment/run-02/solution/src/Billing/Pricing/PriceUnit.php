<?php

declare(strict_types=1);

namespace App\Billing\Pricing;

use App\Shared\Exception\BadInputException;

/**
 * Схемы тарификации из справочника цен:
 *  - per_1m_tokens: цена за 1 000 000 токенов отдельно для каждого вида;
 *  - per_1m_tokens_unified: единая цена за 1 000 000 токенов любого вида.
 */
enum PriceUnit: string
{
    case Per1mTokens = 'per_1m_tokens';
    case Per1mTokensUnified = 'per_1m_tokens_unified';

    public static function fromWire(string $value): self
    {
        return self::tryFrom($value)
            ?? throw new BadInputException(sprintf(
                'Неизвестная схема тарификации «%s» (ожидалось per_1m_tokens или per_1m_tokens_unified)',
                $value,
            ));
    }
}
