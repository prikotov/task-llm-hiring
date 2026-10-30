<?php

declare(strict_types=1);

namespace App\Application\Prices;

/**
 * Окно оф-пик из входного файла (минуты от 00:00 UTC).
 */
final readonly class OffPeakSpec
{
    public function __construct(
        public int $startMinutes,
        public int $endMinutes,
        public string $multiplier,
    ) {
    }
}
