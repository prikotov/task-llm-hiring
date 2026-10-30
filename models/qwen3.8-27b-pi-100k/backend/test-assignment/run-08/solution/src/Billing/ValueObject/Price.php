<?php

declare(strict_types=1);

namespace App\Billing\ValueObject;

use App\Billing\Enum\PricingUnit;
use App\Billing\Import\ImportException;

/**
 * Таблица цен модели на конкретный момент: схема + ставки.
 * Ключи rates — виды токенов: input, output, cache_read, cache_write (разные) или any (единая).
 */
final class Price
{
    /** @param array<string, Decimal> $rates */
    public function __construct(
        public readonly PricingUnit $unit,
        public readonly array $rates,
    ) {
    }

    /**
     * @param array<string, string> $rates decimal-строки по ключам вида токена
     *
     * @throws ImportException если для схемы unit отсутствует обязательная ставка
     */
    public static function create(PricingUnit $unit, array $rates): self
    {
        $byKey = [];
        foreach ($rates as $key => $value) {
            $byKey[(string) $key] = Decimal::of($value);
        }

        foreach ($unit->requiredRateKeys() as $required) {
            if (!isset($byKey[$required])) {
                throw new ImportException(
                    sprintf('Недостаточно ставок для схемы "%s": не найдена ставка "%s".', $unit->value, $required)
                );
            }
        }

        return new self($unit, $byKey);
    }

    public function rate(string $kind): Decimal
    {
        if (!isset($this->rates[$kind])) {
            throw new \LogicException(sprintf('Ставка "%s" отсутствует в цене схемы "%s".', $kind, $this->unit->value));
        }

        return $this->rates[$kind];
    }
}
