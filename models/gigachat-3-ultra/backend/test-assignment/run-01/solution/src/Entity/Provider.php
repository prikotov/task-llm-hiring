<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowStart = null;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowEnd = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 6, nullable: true)]
    private ?string $offPeakMultiplier = null;

    /** @var Collection<int, Model> */
    #[ORM\OneToMany(targetEntity: Model::class, mappedBy: 'provider', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $models;

    /** @var Collection<int, Price> */
    #[ORM\OneToMany(targetEntity: Price::class, mappedBy: 'provider', cascade: ['persist', 'remove'], orphanRemoval: true)]
    private Collection $prices;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
        $this->models = new ArrayCollection();
        $this->prices = new ArrayCollection();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getOffPeakWindowStart(): ?string
    {
        return $this->offPeakWindowStart;
    }

    public function setOffPeakWindowStart(?string $windowStart): void
    {
        $this->offPeakWindowStart = $windowStart;
    }

    public function getOffPeakWindowEnd(): ?string
    {
        return $this->offPeakWindowEnd;
    }

    public function setOffPeakWindowEnd(?string $windowEnd): void
    {
        $this->offPeakWindowEnd = $windowEnd;
    }

    public function getOffPeakMultiplier(): ?string
    {
        return $this->offPeakMultiplier;
    }

    public function setOffPeakMultiplier(?string $multiplier): void
    {
        $this->offPeakMultiplier = $multiplier;
    }

    /** @return Collection<int, Model> */
    public function getModels(): Collection
    {
        return $this->models;
    }

    public function addModel(Model $model): void
    {
        if (!$this->models->contains($model)) {
            $this->models->add($model);
            $model->setProvider($this);
        }
    }

    public function removeModel(Model $model): void
    {
        if ($this->models->contains($model)) {
            $this->models->removeElement($model);
            $model->setProvider(null);
        }
    }

    /** @return Collection<int, Price> */
    public function getPrices(): Collection
    {
        return $this->prices;
    }

    public function addPrice(Price $price): void
    {
        if (!$this->prices->contains($price)) {
            $this->prices->add($price);
            $price->setProvider($this);
        }
    }

    public function removePrice(Price $price): void
    {
        if ($this->prices->contains($price)) {
            $this->prices->removeElement($price);
            $price->setProvider(null);
        }
    }
}