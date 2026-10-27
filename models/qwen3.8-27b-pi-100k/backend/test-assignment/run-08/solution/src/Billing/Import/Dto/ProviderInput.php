<?php

declare(strict_types=1);

namespace App\Billing\Import\Dto;

use App\Billing\ValueObject\OffPeak;

/** Валидированное описание провайдера из файла истории цен. */
final class ProviderInput
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?OffPeak $offPeak,
    ) {
    }
}
