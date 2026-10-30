<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'models', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uq_models_provider_code', columns: ['provider_id', 'code']),
])]
final class Model
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'models')]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private Provider $provider;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code;

    /** @var Collection<int, Price> */
    #[ORM\OneToMany(targetEntity: Price::class, mappedBy: 'model')]
    private Collection $prices;

    public function __construct(Provider $provider, string $code)
    {
        $this->provider = $provider;
        $this->code = $code;
        $this->prices = new ArrayCollection();
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    /** Идентификатор модели в отчётах: `<provider>/<code>`. */
    public function fullName(): string
    {
        return $this->provider->code.'/'.$this->code;
    }
}
