<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use App\Domain\Billing\Cost;

/**
 * Строка цен модели, действующая с `validFrom` (включительно) до следующей строки
 * (или бесконечности) той же модели.
 *
 * Иммутабельная: коррекция цены (reprice) создаёт новую строку,
 * замена в справочнике идёт на уровне persistence-слоя.
 *
 * @readonly
 */
final class PriceRow
{
    /**
     * @param Cost[] $rates строковые ставки за 1 000 000 токенов
     *                      (per-token: input/output/cache_read/cache_write; unified: any)
     */
    public function __construct(
        public readonly string $providerCode,
        public readonly string $modelCode,
        public readonly \DateTimeImmutable $validFrom,
        public readonly string $currency,
        public readonly BillingUnit $unit,
        public readonly array $rates,
    ) {
    }
}
