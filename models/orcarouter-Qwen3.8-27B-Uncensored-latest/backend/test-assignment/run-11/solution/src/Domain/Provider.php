<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
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

    /** Минуты суток UTC (0–1439); null — оф-пик не задан. */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $offPeakStartMinutes = null;

    /** Минуты суток UTC (0–1439); исключительная граница; окно может пересекать полночь. */
    #[ORM\Column(type: 'integer', nullable: true)]
    private ?int $offPeakEndMinutes = null;

    /** Строка, например "0.5". */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    private ?string $offPeakMultiplier = null;

    /** @var Collection<int, Model> */
    #[ORM\OneToMany(targetEntity: Model::class, mappedBy: 'provider', cascade: ['remove'], orphanRemoval: true)]
    private Collection $models;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
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

    public function setOffPeakStartMinutes(?int $v): void
    {
        $this->offPeakStartMinutes = $v;
    }

    public function setOffPeakEndMinutes(?int $v): void
    {
        $this->offPeakEndMinutes = $v;
    }

    public function setOffPeakMultiplier(?string $v): void
    {
        $this->offPeakMultiplier = $v;
    }

    public function hasOffPeak(): bool
    {
        return $this->offPeakStartMinutes !== null
            && $this->offPeakEndMinutes !== null
            && $this->offPeakMultiplier !== null;
    }

    /**
     * True, если ts (UTC) попадает в закрытое слева, открытое справа окно [start, end),
     * с учётом перехода через полночь.
     */
    public function isInOffPeak(\DateTimeImmutable $utcTs): bool
    {
        if (!$this->hasOffPeak()) {
            return false;
        }

        $minutes = (int) $utcTs->format('G') * 60 + (int) $utcTs->format('i');
        $start = $this->offPeakStartMinutes;
        $end   = $this->offPeakEndMinutes;

        if ($start === $end) {
            return false;
        }

        return $start < $end
            ? $minutes >= $start && $minutes < $end
            : $minutes >= $start || $minutes < $end; // окно пересекает полночь
    }

    /**
     * Money-множитель (из строки в БД).
     */
    public function offPeakMultiplier(): ?Money
    {
        return $this->offPeakMultiplier !== null
            ? Money::fromString($this->offPeakMultiplier)
            : null;
    }

    /** @return Collection<int, Model> */
    public function getModels(): Collection
    {
        return $this->models;
    }
}
