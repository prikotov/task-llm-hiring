<?php

declare(strict_types=1);

namespace App\Billing\Application\Import;

use App\Billing\Domain\Catalog\OffPeakWindow;
use App\Billing\Domain\Catalog\PricingUnit;
use DateTimeImmutable;

/** Разобранные и проверенные данные файла цен (до записи в БД). */
final readonly class PriceCatalogData
{
    /**
     * @param list<array{code: string, name: string, offPeak: ?OffPeakWindow}>                                   $providers
     * @param list<array{provider: string, code: string}>                                                         $models
     * @param list<array{provider: string, model: string, validFrom: DateTimeImmutable, currency: string, unit: PricingUnit, rates: array<string, string>}> $prices
     */
    public function __construct(
        public array $providers,
        public array $models,
        public array $prices,
    ) {
    }
}
