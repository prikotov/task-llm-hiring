<?php

declare(strict_types=1);

namespace App\Ingest;

use App\Domain\OffPeak;

/**
 * Строка файла цен в части провайдеров.
 *
 * @readonly
 */
final class ProviderData
{
    /**
     * Трехчастное состояние off_peak из файла:
     *  - offPeak не null — окно задано (применяется);
     *  - offPeak null и offPeakSpecified true — явная null (окно снимается);
     *  - offPeak null и offPeakSpecified false — поле отсутствовало (существующее сохранять).
     */
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?OffPeak $offPeak,
        public readonly bool $offPeakSpecified,
    ) {
    }
}
