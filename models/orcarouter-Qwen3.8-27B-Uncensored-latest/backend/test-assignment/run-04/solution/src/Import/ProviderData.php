<?php

declare(strict_types=1);

namespace App\Import;

/** @readonly */
final class ProviderData
{
    /** @param array{window_start: string, window_end: string, multiplier: string}|null $offPeak */
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?array $offPeak,
    ) {
    }
}
