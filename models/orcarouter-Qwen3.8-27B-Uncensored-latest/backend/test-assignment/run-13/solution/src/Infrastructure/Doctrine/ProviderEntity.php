<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'billing_providers')]
class ProviderEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    public int $id;

    #[ORM\Column(type: Types::STRING, unique: true, length: 64)]
    public string $code;

    #[ORM\Column(type: Types::STRING, length: 128)]
    public string $name;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public ?int $offPeakStartMin = null;

    #[ORM\Column(type: Types::INTEGER, nullable: true)]
    public ?int $offPeakEndMin = null;

    #[ORM\Column(type: Types::STRING, length: 16, nullable: true)]
    public ?string $offPeakMultiplier = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $createdAt;
}
