<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uq_provider_code', columns: ['code'])]
final class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    public int|null $id = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $code = '';

    #[ORM\Column(type: Types::STRING, length: 128)]
    public string $name = '';

    #[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
    public ?string $offPeakStart = null;

    #[ORM\Column(type: Types::STRING, length: 5, nullable: true)]
    public ?string $offPeakEnd = null;

    #[ORM\Column(type: Types::STRING, nullable: true)]
    public ?string $offPeakMultiplier = null;
}
