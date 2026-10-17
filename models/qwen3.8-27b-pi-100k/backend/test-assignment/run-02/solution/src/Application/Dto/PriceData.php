<?php

declare(strict_types=1);

namespace App\Application\Dto;

/**
 * Разобранная и проверенная строка цен из файла.
 */
final class PriceData
{
    /**
     * @param array<string, string> $rates ставки — десятичные строки
     */
    public function __construct(
        public readonly string $provider,
        public readonly string $model,
        public readonly \DateTimeImmutable $validFrom,
        public readonly string $currency,
        public readonly string $unit,
        public readonly array $rates,
    ) {
    }
}
