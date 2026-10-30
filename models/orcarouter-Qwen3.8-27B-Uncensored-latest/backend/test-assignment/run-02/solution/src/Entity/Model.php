<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'catalog_models', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_catalog_models_provider_code', columns: ['provider', 'code']),
])]
final class Model
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $provider;

    #[ORM\Column(type: 'string', length: 128)]
    private string $code;

    public function __construct(string $provider, string $code)
    {
        $this->provider = $provider;
        $this->code = $code;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getCode(): string
    {
        return $this->code;
    }
}
