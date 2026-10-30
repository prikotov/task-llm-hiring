<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цены модели с момента `validFrom` (включительно).
 *
 * Натуральный ключ — `(provider, model, validFrom)`. `unit` задаёт схему:
 *  - `per_1m_tokens`: ставки `input|output|cache_read|cache_write` за 1M токенов;
 *  - `per_1m_tokens_unified`: единственная ставка `any` за 1M токенов любого вида.
 *
 * `rates` — значения ставки, десятичные строки (≤ 6 знаков).
 */
#[ORM\Entity]
#[ORM\Table(name: 'prices')]
final class PriceRow
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: Types::STRING, length: 64)]
        public string $providerCode,

        #[ORM\Id]
        #[ORM\Column(type: Types::STRING, length: 64)]
        public string $modelCode,

        #[ORM\Id]
        #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
        public \DateTimeImmutable $validFrom,

        #[ORM\Column(type: Types::STRING, length: 3)]
        public string $currency = 'USD',

        #[ORM\Column(type: Types::STRING, length: 32)]
        public string $unit,

        /** @var array<string, string> */
        #[ORM\Column(type: Types::JSONB)]
        public array $rates = [],
    ) {
    }
}
