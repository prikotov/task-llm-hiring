<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\Index(name: 'price_lookup', columns: ['provider_code', 'model_code', 'valid_from'])]
class PriceRow
{
    public const UNIT_PER_M = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public readonly string $providerCode,

        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public readonly string $modelCode,

        #[ORM\Id]
        #[ORM\Column(type: 'datetime_immutable')]
        public readonly \DateTimeImmutable $validFrom,

        #[ORM\Column(type: 'string')]
        public readonly string $currency,

        #[ORM\Column(type: 'string')]
        public readonly string $unit,

        // input / output / ... / any -> rate за 1 000 000 токенов
        #[ORM\Column(type: 'json')]
        public readonly array $rates,
    ) {
    }
}
