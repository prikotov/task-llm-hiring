<?php

declare(strict_types=1);

namespace App\Domain\Pricing;

use InvalidArgumentException;

/**
 * Провайдер LLM. Хранит код, имя и параметры оф-пикового окна.
 */
final class Provider
{
    public function __construct(
        public readonly string $code,
        public readonly string $name,
        public readonly ?OffPeakWindow $offPeak = null,
        public ?int $id = null,
    ) {
        if ('' === $code) {
            throw new InvalidArgumentException('Код провайдера не может быть пустым.');
        }
    }
}
