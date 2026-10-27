<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Модель LLM провайдера (справочник). Естественный ключ (provider, code).
 */
#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uq_models_provider_code', columns: ['provider', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Provider $provider = null;

    #[ORM\Column(length: 64, nullable: false)]
    private string $code = '';

    public function __construct(Provider $provider, string $code)
    {
        $this->provider = $provider;
        $this->code = $code;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function provider(): Provider
    {
        return $this->provider;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function setProvider(Provider $provider): void
    {
        $this->provider = $provider;
    }

    public function qualifiedName(): string
    {
        return $this->provider->code() . '/' . $this->code;
    }
}
