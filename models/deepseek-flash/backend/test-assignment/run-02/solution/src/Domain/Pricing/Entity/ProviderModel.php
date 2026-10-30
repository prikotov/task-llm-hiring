<?php

declare(strict_types=1);

namespace App\Domain\Pricing\Entity;

use App\Domain\Provider\Entity\Provider;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'provider_models')]
#[ORM\UniqueConstraint(name: 'uniq_provider_models_provider_code', columns: ['provider_id', 'code'])]
final class ProviderModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', nullable: false, onDelete: 'CASCADE')]
    private Provider $provider;

    #[ORM\Column(type: 'string', length: 128)]
    private string $code;

    /** @var Collection<int, Price> */
    #[ORM\OneToMany(targetEntity: Price::class, mappedBy: 'providerModel')]
    #[ORM\OrderBy(['validFrom' => 'ASC'])]
    private Collection $prices;

    public function __construct(Provider $provider, string $code)
    {
        $this->provider = $provider;
        $this->code = $code;
        $this->prices = new ArrayCollection();
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

    public function label(): string
    {
        return $this->provider->code().'/'.$this->code;
    }
}
