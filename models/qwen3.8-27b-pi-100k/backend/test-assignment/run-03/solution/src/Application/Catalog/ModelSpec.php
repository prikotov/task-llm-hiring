<?php

declare(strict_types=1);

namespace App\Application\Catalog;

/** Модель из файла цен (проверенная запись справочника). */
final class ModelSpec
{
    public function __construct(
        public readonly string $provider,
        public readonly string $code,
    ) {
    }
}
