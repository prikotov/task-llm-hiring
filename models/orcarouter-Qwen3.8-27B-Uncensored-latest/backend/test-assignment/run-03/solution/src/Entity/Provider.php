<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\OffPeak;
use App\Repository\ProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uniq_providers_code', columns: ['code'])]
final class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakStart = null; // 'HH:MM' (UTC)

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakEnd = null;   // 'HH:MM' (UTC)

    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $offPeakMultiplier = null; // десятичная строка, напр. "0.5"

    /** @var Collection<int, LlmModel> */
    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: LlmModel::class)]
    private Collection $models;

    public function __construct()
    {
        $this->models = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getOffPeakStart(): ?string
    {
        return $this->offPeakStart;
    }

    public function getOffPeakEnd(): ?string
    {
        return $this->offPeakEnd;
    }

    public function getOffPeakMultiplier(): ?string
    {
        return $this->offPeakMultiplier;
    }

    public function setOffPeak(?string $start, ?string $end, ?string $multiplier): self
    {
        $this->offPeakStart = $start;
        $this->offPeakEnd = $end;
        $this->offPeakMultiplier = $multiplier;

        return $this;
    }

    /** Возвращает OffPeak, если у провайдера есть окно скидки, иначе null. */
    public function offPeak(): ?OffPeak
    {
        if ($this->offPeakStart === null || $this->offPeakEnd === null || $this->offPeakMultiplier === null) {
            return null;
        }

        return new OffPeak($this->offPeakStart, $this->offPeakEnd, $this->offPeakMultiplier);
    }

    /** @return Collection<int, LlmModel> */
    public function getModels(): Collection
    {
        return $this->models;
    }
}
