<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Billing\OffPeak;
use App\Repository\ProviderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM.
 *
 * Оф-пиковое окно и множитель хранятся отдельными колонками (значения —
 * в минутах внутри суток и строкой-децималкой, чтобы избежать float для денег).
 */
#[ORM\Entity(repositoryClass: ProviderRepository::class)]
#[ORM\Table(name: 'providers')]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $code;

    #[ORM\Column(type: 'string', length: 128)]
    private string $name;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $offPeakStartMin = null;

    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $offPeakEndMin = null;

    /**
     * Множитель цены в оф-пике (десятичная строка, напр. "0.5").
     */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $offPeakMultiplier = null;

    /**
     * @var Collection<Model>
     */
    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Model::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $models;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
        $this->models = new ArrayCollection();
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setOffPeak(?string $startHM, ?string $endHM, ?string $multiplier): void
    {
        if ($startHM === null || $endHM === null || $multiplier === null) {
            $this->offPeakStartMin = null;
            $this->offPeakEndMin = null;
            $this->offPeakMultiplier = null;

            return;
        }

        $this->offPeakStartMin = OffPeak::minutesFromHM($startHM);
        $this->offPeakEndMin = OffPeak::minutesFromHM($endHM);
        $this->offPeakMultiplier = $multiplier;
    }

    public function offPeak(): ?OffPeak
    {
        if ($this->offPeakStartMin === null || $this->offPeakEndMin === null || $this->offPeakMultiplier === null) {
            return null;
        }

        return new OffPeak($this->offPeakStartMin, $this->offPeakEndMin, $this->offPeakMultiplier);
    }

    public function models(): \Doctrine\Common\Collections\Collection
    {
        return $this->models;
    }
}
