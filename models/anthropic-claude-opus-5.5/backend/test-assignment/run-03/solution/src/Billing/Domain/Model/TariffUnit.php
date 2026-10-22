<?php

declare(strict_types=1);

namespace App\Billing\Domain\Model;

/**
 * Схема тарификации провайдера (поле "unit" в истории цен).
 */
enum TariffUnit: string
{
    /** Цена за 1 000 000 токенов отдельно по каждому виду токенов. */
    case PerMillionPerKind = 'per_1m_tokens';

    /** Единая цена за 1 000 000 токенов любого вида. */
    case PerMillionUnified = 'per_1m_tokens_unified';

    /**
     * Виды токенов, для которых обязательна ставка при данной схеме.
     *
     * @return list<string>
     */
    public function requiredRateKeys(): array
    {
        return match ($this) {
            self::PerMillionPerKind => ['input', 'output', 'cache_read', 'cache_write'],
            self::PerMillionUnified => ['any'],
        };
    }
}
