<?php

declare(strict_types=1);

namespace App\Import;

/** Модель из файла истории цен (проверенная парсером). */
final class ModelSpec
{
    public function __construct(
        private readonly string $providerCode,
        private readonly string $code,
    ) {
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
