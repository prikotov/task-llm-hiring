<?php

declare(strict_types=1);

namespace App\Domain;

/** Схема тарификации — тип цены из справочника. */
enum Unit: string
{
    /** Цена за 1 000 000 токенов отдельно для каждого вида. */
    case PerToken = 'per_1m_tokens';

    /** Единая цена за 1 000 000 токенов любого вида. */
    case PerTokenUnified = 'per_1m_tokens_unified';

    /**
     * @throws \DomainException
     */
    public static function fromWire(string $value): self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }

        $allowed = implode(', ', array_map(static fn (self $c): string => $c->value, self::cases()));

        throw new \DomainException(sprintf('Неизвестная схема тарификации "%s"; допустимые значения: %s.', $value, $allowed));
    }
}
