<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Provider\Provider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'model')]
#[ORM\UniqueConstraint(name: 'uniq_model_provider_code', columns: ['provider', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'models')]
    #[ORM\JoinColumn(name: 'provider', referencedColumnName: 'id', nullable: false)]
    private Provider $provider;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code = '';

    /** @var Collection<int, PriceLine> */
    #[ORM\OneToMany(mappedBy: 'model', targetEntity: PriceLine::class)]
    private Collection $priceLines;

    public function __construct()
    {
        $this->priceLines = new ArrayCollection();
    }

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

    /** @return Collection<int, PriceLine> */
    public function getPriceLines(): Collection
    {
        return $this->priceLines;
    }
}
