<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use InvalidArgumentException;

/**
 * Схемы тарификации ценовых строк:
 * - per_1m_tokens           — разбивка по видам токенов (input/output/cache_read/cache_write);
 * - per_1m_tokens_unified   — единая цена «any».
 * Оба случая — цена за 1 000 000 токенов.
 */
enum PricingScheme: string
{
    case PER_TOKEN = 'per_1m_tokens';
    case PER_TOKEN_UNIFIED = 'per_1m_tokens_unified';

    /** Конструкция из строки unit. */
    public static function parse(string $unit): self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $unit) {
                return $case;
            }
        }

        throw new InvalidArgumentException(sprintf('Unknown pricing unit: "%s".', $unit));
    }

    /**
     * @param list<string> $units
     */
    public static function fromAny(array $units): self
    {
        foreach ($units as $u) {
            if ($u === self::PER_TOKEN->value || $u === self::PER_TOKEN_UNIFIED->value) {
                return self::parse($u);
            }
        }

        throw new InvalidArgumentException('Unknown unit: ' . implode(',', $units));
    }
}
