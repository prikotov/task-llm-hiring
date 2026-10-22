<?php

declare(strict_types=1);

namespace App\Billing\Domain\Model;

use App\Billing\Domain\Money\Money;

/**
 * Ставки одной строки цен. Набор ключей определяется схемой тарификации:
 *   per_1m_tokens            → input, output, cache_read, cache_write
 *   per_1m_tokens_unified    → any
 */
final readonly class RateCard
{
    /**
     * @param array<string, Money> $rates
     */
    private function __construct(
        private TariffUnit $unit,
        private array $rates,
    ) {
    }

    /**
     * @param array<string, Money> $rates
     */
    public static function of(TariffUnit $unit, array $rates): self
    {
        $required = $unit->requiredRateKeys();
        $missing = array_diff($required, array_keys($rates));
        $unknown = array_diff(array_keys($rates), $required);

        if ([] !== $missing) {
            throw new \InvalidArgumentException(sprintf(
                'Для схемы "%s" не хватает ставок: %s',
                $unit->value,
                implode(', ', $missing),
            ));
        }

        if ([] !== $unknown) {
            throw new \InvalidArgumentException(sprintf(
                'Для схемы "%s" лишние ставки: %s',
                $unit->value,
                implode(', ', $unknown),
            ));
        }

        return new self($unit, $rates);
    }

    public function unit(): TariffUnit
    {
        return $this->unit;
    }

    /**
     * @return array<string, Money>
     */
    public function rates(): array
    {
        return $this->rates;
    }

    public function rate(string $key): Money
    {
        return $this->rates[$key] ?? throw new \InvalidArgumentException(sprintf('Нет ставки "%s"', $key));
    }
}
