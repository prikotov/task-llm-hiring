<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Money\Decimal;
use InvalidArgumentException;

/**
 * Строка прайс-листа: цены модели, действующие с момента validFrom
 * (валидность — до следующей строки той же модели).
 */
final class PriceRow
{
    /** @var array<string, Decimal> */
    private array $rates = [];

    /**
     * @param array<string, string|Decimal> $rates
     */
    public function __construct(
        public readonly Provider $provider,
        public readonly Model $model,
        public readonly \DateTimeImmutable $validFrom,
        public readonly PricingUnit $unit,
        array $rates,
        public readonly string $currency = 'USD',
    ) {
        foreach ($unit->requiredRateKeys() as $key) {
            if (!array_key_exists($key, $rates)) {
                throw new InvalidArgumentException(sprintf(
                    'Для схемы "%s" не задана ставка "%s" (провайдер %s, модель %s, valid_from %s).',
                    $unit->value,
                    $key,
                    $provider->code,
                    $model->code,
                    $validFrom->format('Y-m-d\TH:i:s\Z'),
                ));
            }
        }

        foreach ($rates as $key => $value) {
            $this->rates[$key] = $value instanceof Decimal ? $value : Decimal::fromString($value);
        }
    }

    public function rate(string $key): Decimal
    {
        return $this->rates[$key] ?? throw new InvalidArgumentException(sprintf('Нет ставки "%s".', $key));
    }

    /**
     * @return array<string, Decimal>
     */
    public function rates(): array
    {
        return $this->rates;
    }
}
