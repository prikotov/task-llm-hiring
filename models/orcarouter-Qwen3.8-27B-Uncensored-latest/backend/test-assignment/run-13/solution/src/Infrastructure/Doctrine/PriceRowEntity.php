<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'billing_price_rows')]
#[ORM\UniqueConstraint(name: 'uq_price_row', columns: ['provider_code', 'model_code', 'valid_from'])]
class PriceRowEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    public int $id;

    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $providerCode;

    #[ORM\Column(type: Types::STRING, length: 128)]
    public string $modelCode;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $validFrom;

    #[ORM\Column(type: Types::STRING, length: 3)]
    public string $currency;

    #[ORM\Column(type: Types::STRING, length: 40)]
    public string $unit;

    #[ORM\Column(type: Types::TEXT)]
    public string $ratesJson = '{}';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $createdAt;
}
