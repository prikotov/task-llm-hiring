<?php

declare(strict_types=1);

namespace App\Import;

use App\Domain\OffPeakWindow;

/** Строка секции "providers" файла цен. */
final readonly class ProviderDoc
{
    public function __construct(
        public string $code,
        public string $name,
        public ?OffPeakWindow $offPeak,
    ) {
    }
}
