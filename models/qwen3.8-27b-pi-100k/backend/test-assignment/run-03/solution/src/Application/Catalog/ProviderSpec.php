<?php

declare(strict_types=1);

namespace App\Application\Catalog;

use App\Domain\Catalog\OffPeak;

/** Провайдер из файла цен (проверенная запись справочника). */
final class ProviderSpec
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?OffPeak $offPeak,
    ) {
    }
}
