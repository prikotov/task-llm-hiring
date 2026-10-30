<?php

declare(strict_types=1);

namespace App\Pricing\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'llm_model')]
#[ORM\UniqueConstraint(name: 'uniq_model_provider_code', columns: ['provider_id', 'code'])]
class LlmModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    public function __construct(
        #[ORM\ManyToOne(targetEntity: Provider::class)]
        #[ORM\JoinColumn(nullable: false)]
        private Provider $provider,
        #[ORM\Column(length: 128)]
        private string $code,
    ) {
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
}
