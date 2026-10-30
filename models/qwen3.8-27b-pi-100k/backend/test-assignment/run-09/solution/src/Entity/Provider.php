<?php

declare(strict_types=1);

namespace App\Entity;

use App\Service\BillingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM: справочник + параметры оф-пик скидки на уровне провайдера.
 */
#[ORM\Entity(repositoryClass: BillingRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_provider_code', columns: ['code'])]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    /** "HH:MM" — начало окна оф-пика (UTC); null = нет скидки. */
    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowStart = null;

    /** "HH:MM" — конец окна оф-пика (UTC), исключаемо; null = нет скидки. */
    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowEnd = null;

    /** Множитель оф-пика (decimal). null вместе с окном = нет скидки. */
    #[ORM\Column(type: 'decimal', precision: 20, scale: 8, nullable: true)]
    private ?string $offPeakMultiplier = null;

    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: TokenModel::class, orphanRemoval: true)]
    private Collection $models;

    public function __construct()
    {
        $this->models = new ArrayCollection();
    }

    public function getId(): ?int
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

    public function hasOffPeak(): bool
    {
        return null !== $this->offPeakWindowStart
            && null !== $this->offPeakWindowEnd
            && null !== $this->offPeakMultiplier;
    }

    public function setOffPeak(?string $windowStart, ?string $windowEnd, ?string $multiplier): self
    {
        $this->offPeakWindowStart = $windowStart;
        $this->offPeakWindowEnd = $windowEnd;
        $this->offPeakMultiplier = $multiplier;

        return $this;
    }

    public function getOffPeakWindowStart(): ?string
    {
        return $this->offPeakWindowStart;
    }

    public function getOffPeakWindowEnd(): ?string
    {
        return $this->offPeakWindowEnd;
    }

    public function getOffPeakMultiplier(): ?string
    {
        return $this->offPeakMultiplier;
    }

    /** @return Collection<int, TokenModel> */
    public function getModels(): Collection
    {
        return $this->models;
    }

    public function addModel(TokenModel $model): self
    {
        if (!$this->models->contains($model)) {
            $this->models->add($model);
            $model->setProvider($this);
        }

        return $this;
    }
}
