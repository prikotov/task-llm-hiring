<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uq_price_row', columns: ['provider', 'model', 'valid_from'])]
#[ORM\Index(columns: ['valid_from'])]
final class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    public int|null $id = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $provider = '';

    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $model = '';

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public DateTimeImmutable $validFrom;

    #[ORM\Column(type: Types::STRING, length: 32)]
    public string $unit = '';

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $rateInput = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $rateOutput = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $rateCacheRead = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $rateCacheWrite = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $rateAny = null;
}

