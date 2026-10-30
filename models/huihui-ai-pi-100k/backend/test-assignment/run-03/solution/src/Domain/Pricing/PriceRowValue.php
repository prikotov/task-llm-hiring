<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use DateTimeImmutable;

/**
 * Строка цен модели: с какого момента, какой схемой тарификации,
 * с какими ставками (точные десятичные строки).
 */
final class PriceRowValue
{
    /**
     * @param array<string, string> $rates ставка по каждому ожидаемому виду токенов
     *                                     (ключи — строки TokenKind::value)
     */
    public function __construct(
        public readonly string $providerCode,
        public readonly string $modelCode,
        public readonly DateTimeImmutable $validFrom,
        public readonly string $currency,
        public readonly PriceUnit $unit,
        public readonly array $rates,
    ) {
    }
}
