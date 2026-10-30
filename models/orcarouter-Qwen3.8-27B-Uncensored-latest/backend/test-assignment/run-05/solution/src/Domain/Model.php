<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'billing_model')]
#[ORM\UniqueConstraint(name: 'uniq_billing_model_provider_code', columns: ['provider_id', 'code'])]
class Model
{
    public function __construct(
        #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
        private ?int $id,

        #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'models')]
        #[ORM\JoinColumn(name: 'provider_id', nullable: false, onDelete: 'CASCADE')]
        private Provider $provider,

        // Код модели в рамках провайдера (естественный ключ вместе с провайдером).
        #[ORM\Column(type: 'string', length: 128)]
        private string $code = '',
    ) {
    }

    public static function make(Provider $provider, string $code): self
    {
        return new self(id: null, provider: $provider, code: $code);
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    /** Идентификатор вида "<provider>/<code>" (используется в отчёте). */
    public function fullName(): string
    {
        return $this->provider->getCode().'/'. $this->code;
    }
}
