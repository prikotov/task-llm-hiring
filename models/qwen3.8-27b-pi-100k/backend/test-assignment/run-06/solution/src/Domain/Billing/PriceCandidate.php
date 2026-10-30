<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Кандидат: строка цен, применимая к событию (valid_from <= ts события),
 * вместе с оф-пиком провайдера для последующего расчёта.
 */
final class PriceCandidate
{
    /**
     * @param array<string, string> $rates
     */
    public function __construct(
        public readonly int $priceRowId,
        public readonly \DateTimeImmutable $validFrom,
        public readonly PriceUnit $unit,
        public readonly array $rates,
        public readonly ?OffPeakWindow $offPeak,
    ) {}
}
