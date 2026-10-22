<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Domain\Pricing\Model;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uniq_models_provider_code', columns: ['provider_id', 'code'])]
final class ModelEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ProviderEntity::class)]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private ProviderEntity $provider;

    #[ORM\Column(type: 'string', length: 128)]
    private string $code;

    public function __construct(ProviderEntity $provider, string $code)
    {
        $this->provider = $provider;
        $this->code = $code;
    }

    public function toDomain(): Model
    {
        return new Model($this->code, $this->id);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function provider(): ProviderEntity
    {
        return $this->provider;
    }

    public function code(): string
    {
        return $this->code;
    }
}
