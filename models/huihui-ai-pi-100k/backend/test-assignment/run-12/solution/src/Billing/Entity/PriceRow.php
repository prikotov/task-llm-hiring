<?php

declare(strict_types=1);

namespace App\Billing\Entity;

#[\Doctrine\ORM\Mapping\Entity]
#[\Doctrine\ORM\Mapping\Table(name: 'price_rows')]
#[\Doctrine\ORM\Mapping\UniqueConstraint(name: 'uniq_price_row', columns: ['provider_code', 'model_code', 'valid_from'])]
#[\Doctrine\ORM\Mapping\Index(name: 'idx_price_model_date', columns: ['provider_code', 'model_code', 'valid_from'])]
class PriceRow
{
    /** Пустая string; <providerCode>|<modelCode>|<validFrom UTC unix ts>. */
    public function __construct(
        #[\Doctrine\ORM\Mapping\Id]
        #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 255)]
        #[\Doctrine\ORM\Mapping\GeneratedValue(strategy: 'NONE')]
        public readonly string $id,
    ) {}

    #[\Doctrine\ORM\Mapping\Column(name: 'provider_code', type: 'string', length: 64)]
    public string $providerCode = '';

    #[\Doctrine\ORM\Mapping\Column(name: 'model_code', type: 'string', length: 128)]
    public string $modelCode = '';

    #[\Doctrine\ORM\Mapping\Column(name: 'valid_from', type: 'datetime_immutable')]
    public \DateTimeImmutable $validFrom;

    #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 8)]
    public string $currency = 'USD';

    /** per_1m_tokens | per_1m_tokens_unified */
    #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 32)]
    public string $unit = '';

    /**
     * JSON rates. Values — decimal бcmath (строка).
     *
     * @var array<string, string>
     */
    #[\Doctrine\ORM\Mapping\Column(type: 'json')]
    public array $rates = [];
}
