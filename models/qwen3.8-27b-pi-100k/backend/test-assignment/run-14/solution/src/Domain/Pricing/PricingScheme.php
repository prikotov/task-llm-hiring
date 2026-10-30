<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use InvalidArgumentException;

/**
 * Схема тарификации модели.
 *
 * per_1m_tokens:        цена за 1 000 000 токенов отдельно для каждого вида
 *                       (input, output, cache_read, cache_write);
 * per_1m_tokens_unified: единая цена за 1 000 000 токенов любого вида (any).
 */
enum PricingScheme: string
{
    case PerToken = 'per_1m_tokens';
    case Unified = 'per_1m_tokens_unified';

    public static function parse(string $value): self
    {
        $case = self::tryFrom($value);
        if ($case === null) {
            throw new InvalidArgumentException(
                sprintf('Unknown pricing scheme "%s". Expected one of: %s.',
                    $value,
                    implode(', ', array_map(fn (self $c) => $c->value, self::cases())),
                ),
            );
        }
        return $case;
    }

    /**
     * Возвращает ключи ставок, ожидаемые для схемы.
     */
    public function expectedRateKeys(): array
    {
        return match ($this) {
            self::PerToken => ['input', 'output', 'cache_read', 'cache_write'],
            self::Unified => ['any'],
        };
    }
}
