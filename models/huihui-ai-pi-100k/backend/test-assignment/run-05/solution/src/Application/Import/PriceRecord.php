<?php

declare(strict_types=1);

namespace App\Application\Import;

use App\Domain\Billing\PriceRowSpec;

/**
 * Запрошенная строка цен из файла справочника.
 */
final class PriceRecord
{
    public function __construct(
        public readonly string $provider,
        public readonly string $model,
        public readonly \DateTimeImmutable $validFrom,
        public readonly string $currency,
        public readonly PriceRowSpec $spec,
    ) {
    }
}
