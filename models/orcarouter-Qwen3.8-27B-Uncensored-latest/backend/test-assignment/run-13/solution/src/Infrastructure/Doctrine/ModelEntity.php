<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'billing_models')]
#[ORM\UniqueConstraint(name: 'uq_model_key', columns: ['provider_code', 'code'])]
class ModelEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    public int $id;

    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $providerCode;

    #[ORM\Column(type: Types::STRING, length: 128)]
    public string $code;

    #[ORM\Column(type: Types::STRING, length: 128, nullable: true)]
    public ?string $modelName = null;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $createdAt;
}
