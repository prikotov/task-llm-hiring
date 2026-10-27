<?php

declare(strict_types=1);

namespace App\Entity;

use App\Service\BillingRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * Модель провайдера. Естественный ключ — (provider, code).
 */
#[ORM\Entity(repositoryClass: BillingRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_token_model_provider_code', columns: ['provider_id', 'code'])]
#[ORM\Index(name: 'idx_token_model_provider', fields: ['provider'])]
class TokenModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'models')]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private Provider $provider;

    /** Код модели в контексте провайдера. */
    #[ORM\Column(type: 'string', length: 64)]
    private string $code;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function setProvider(Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }
}
