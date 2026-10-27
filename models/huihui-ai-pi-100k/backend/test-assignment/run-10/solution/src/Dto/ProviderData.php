<?php

declare(strict_types=1);

namespace App\Dto;

/**
 * Провайдер из файла цен (проверенный парсером).
 */
final class ProviderData
{
    /**
     * null — оф-пика нет; иная — [window_start, window_end, multiplier].
     *
     * @param array{window_start:string,window_end:string,multiplier:string}|null $offPeak
     */
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?array $offPeak,
    ) {
    }
}
