<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Схемы тарификации (значения поля `unit` справочника цен).
 */
final class Unit
{
    /** Раздельная цена за 1M токенов для каждого вида. */
    public const PER_1M_TOKENS = 'per_1m_tokens';

    /** Единая цена за 1M токенов любого вида. */
    public const PER_1M_TOKENS_UNIFIED = 'per_1m_tokens_unified';

    /** Виды токенов для схемы per_1m_tokens. */
    public const KIND_INPUT = 'input';
    public const KIND_OUTPUT = 'output';
    public const KIND_CACHE_READ = 'cache_read';
    public const KIND_CACHE_WRITE = 'cache_write';

    /** Единый вид для схемы unified. */
    public const KIND_ANY = 'any';

    public static function isValid(string $unit): bool
    {
        return self::PER_1M_TOKENS === $unit || self::PER_1M_TOKENS_UNIFIED === $unit;
    }

    /**
     * Канонические виды ставок для схемы.
     *
     * @return list<string>
     */
    public static function canonicalKinds(string $unit): array
    {
        return match ($unit) {
            self::PER_1M_TOKENS => [
                self::KIND_INPUT,
                self::KIND_OUTPUT,
                self::KIND_CACHE_READ,
                self::KIND_CACHE_WRITE,
            ],
            self::PER_1M_TOKENS_UNIFIED => [self::KIND_ANY],
            default => throw new \DomainException(sprintf('Неизвестная схема тарификации: %s', $unit)),
        };
    }
}
