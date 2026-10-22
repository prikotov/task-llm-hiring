<?php

declare(strict_types=1);

namespace App\Import;

/** Модель из файла справочника цен. */
final class ModelSpec
{
    public function __construct(
        public readonly string $provider,
        public readonly string $code,
    ) {
    }
}
