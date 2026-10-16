<?php

declare(strict_types=1);

namespace App\Import;

use App\Domain\OffPeakWindow;

/**
 * Провайдер из файла истории цен.
 */
final class ProviderDefinition
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?OffPeakWindow $offPeak,
    ) {
    }
}
