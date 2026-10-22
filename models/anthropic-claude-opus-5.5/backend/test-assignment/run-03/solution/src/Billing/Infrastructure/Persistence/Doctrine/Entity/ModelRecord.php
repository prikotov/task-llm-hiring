<?php

declare(strict_types=1);

namespace App\Billing\Infrastructure\Persistence\Doctrine\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Модель провайдера. Ключ уникальности — (provider, code).
 */
#[ORM\Entity]
#[ORM\Table(name: 'llm_model')]
#[ORM\UniqueConstraint(name: 'uniq_llm_model_provider_code', columns: ['provider_id', 'code'])]
final class ModelRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ProviderRecord::class, inversedBy: 'models')]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private ProviderRecord $provider;

    #[ORM\Column(name: 'code', type: Types::STRING, length: 128)]
    private string $code;

    public static function create(ProviderRecord $provider, string $code): self
    {
        $record = new self();
        $record->provider = $provider;
        $record->code = $code;

        return $record;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function provider(): ProviderRecord
    {
        return $this->provider;
    }
}
