<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\ModelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Модель провайдера (та, которую перепродаём).
 * Естественный ключ — (provider, code).
 */
#[ORM\Entity(repositoryClass: ModelRepository::class)]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uq_model_provider_code', columns: ['provider_id', 'code'])]
class Model
{
    /**
     * @var Collection<Model>
     */
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'models')]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    private Provider $provider;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code;

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
}
