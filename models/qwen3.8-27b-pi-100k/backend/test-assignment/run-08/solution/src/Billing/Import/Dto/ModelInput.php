<?php

declare(strict_types=1);

namespace App\Billing\Import\Dto;

/** Валидированное описание модели из файла истории цен. */
final class ModelInput
{
    public function __construct(
        public readonly string $provider,
        public readonly string $code,
    ) {
    }
}
