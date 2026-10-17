<?php

declare(strict_types=1);

namespace App\Domain;

use ValueError;

/**
 * Схема тарификации (единица цены). Задается полем unit файла цен.
 */
enum PricingUnit: string
{
    case PerMillionPerKind = 'per_1m_tokens';
    case PerMillionUnified = 'per_1m_tokens_unified';

    /**
     * @throws ValueError при неизвестной схеме
     */
    public static function fromApiString(string $unit): self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $unit) {
                return $case;
            }
        }

        throw new ValueError('Неизвестная схема тарификации (unit): ' . $unit);
    }

    /**
     * Необходимые ключи ставок для этой схемы.
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
