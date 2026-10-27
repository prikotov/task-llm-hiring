<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models')]
#[ORM\UniqueConstraint(name: 'uq_model_provider_code', columns: ['provider_id', 'code'])]
class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'models')]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private Provider $provider;

    /** @var Collection<int, PriceRow> */
    #[ORM\OneToMany(targetEntity: PriceRow::class, mappedBy: 'model')]
    private Collection $priceRows;

    public function __construct(string $code, Provider $provider)
    {
        $this->code = $code;
        $this->provider = $provider;
        $this->priceRows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    /** Ключ вида "provider/code" — используется в отчётах. */
    public function getReportKey(): string
    {
        return $this->provider->getCode().'/'. $this->code;
    }

    /** @return Collection<int, PriceRow> */
    public function getPriceRows(): Collection
    {
        return $this->priceRows;
    }
}
