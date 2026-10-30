<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

/**
 * Провайдер из файла истории цен.
 *
 * offPeak: null (скидки нет) либо массив
 * ['start' => '16:30', 'end' => '00:30', 'multiplier' => '0.5'].
 *
 * @readonly
 */
final class ProviderSpec
{
    /**
     * @param array{start: string, end: string, multiplier: string}|null $offPeak
     */
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?array $offPeak,
    ) {
    }
}
