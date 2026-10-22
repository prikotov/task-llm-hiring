<?php

declare(strict_types=1);

namespace App\Entity;

use App\Billing\OffPeakWindow;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\Index(name: 'idx_providers_code', columns: ['code'])]
class Provider
{
    /** Минуты в сутки (0..1439). */
    public const MINUTES_PER_DAY = 1440;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    /** Натуральный код провайдера (уникален). */
    #[ORM\Column(type: 'string', length: 64, unique: true)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(name: 'off_peak_start_minutes', type: 'integer', nullable: true)]
    private ?int $offPeakStartMinutes = null;

    #[ORM\Column(name: 'off_peak_end_minutes', type: 'integer', nullable: true)]
    private ?int $offPeakEndMinutes = null;

    /** Множитель стоимости оф-пиковых событий (десятичная строка, bcmath). */
    #[ORM\Column(name: 'off_peak_multiplier', type: 'string', length: 32, nullable: true)]
    private ?string $offPeakMultiplier = null;

    /** @var Collection<int, Model> */
    #[ORM\OneToMany(targetEntity: Model::class, mappedBy: 'provider')]
    private Collection $models;

    public function __construct(string $code)
    {
        $this->code = $code;
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /** Заодно сбрасывает окно скидки (null = скидки нет). */
    public function setOffPeak(?OffPeakWindow $window): void
    {
        $this->offPeakStartMinutes = $window?->startMinutes();
        $this->offPeakEndMinutes = $window?->endMinutes();
        $this->offPeakMultiplier = $window?->multiplier();
    }

    public function offPeak(): ?OffPeakWindow
    {
        if ($this->offPeakStartMinutes === null
            || $this->offPeakEndMinutes === null
            || $this->offPeakMultiplier === null
        ) {
            return null;
        }

        return new OffPeakWindow($this->offPeakStartMinutes, $this->offPeakEndMinutes, $this->offPeakMultiplier);
    }

    /** @return Collection<int, Model> */
    public function getModels(): Collection
    {
        return $this->models;
    }
}
