<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(
    name: 'models',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uniq_models_provider_code', columns: ['provider_id', 'code']),
    ],
)]
final class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'provider_id', type: Types::INTEGER)]
    private int $providerId;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $code;

    public function __construct(int $providerId, string $code)
    {
        $this->providerId = $providerId;
        $this->code = $code;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
