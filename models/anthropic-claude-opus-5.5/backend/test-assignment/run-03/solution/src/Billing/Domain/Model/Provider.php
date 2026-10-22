<?php

declare(strict_types=1);

namespace App\Billing\Domain\Model;

/**
 * Провайдер LLM. Код уникален (ключ upsert'а), название — описательное.
 * Оф-пиковое окно — свойство провайдера, поэтому живёт здесь же.
 */
final readonly class Provider
{
    public function __construct(
        public string $code,
        public string $name,
        public ?OffPeakWindow $offPeak,
    ) {
        if ('' === trim($code)) {
            throw new \InvalidArgumentException('Код провайдера обязателен');
        }
    }
}
