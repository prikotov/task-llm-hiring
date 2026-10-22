<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\LlmModelRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: LlmModelRepository::class)]
#[ORM\Table(name: 'catalog_models')]
#[ORM\UniqueConstraint(name: 'uniq_catalog_models_provider_code', columns: ['provider_id', 'code'])]
final class LlmModel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'models')]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private Provider $provider;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code;

    /** @var Collection<int, Price> */
    #[ORM\OneToMany(mappedBy: 'model', targetEntity: Price::class)]
    private Collection $prices;

    public function __construct()
    {
        $this->prices = new ArrayCollection();
    }

    public function getId(): int
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

    /** Ключ модели в отчётах: `<provider>/<code>`. */
    public function qualifiedCode(): string
    {
        return $this->provider->getCode() . '/' . $this->code;
    }
}
