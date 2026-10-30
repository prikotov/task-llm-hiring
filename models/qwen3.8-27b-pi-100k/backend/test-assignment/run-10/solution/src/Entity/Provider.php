<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер LLM и его оф-пиковое окно скидки (UTC).
 */
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uniq_provider_code', columns: ['code'])]
class Provider extends AbstractEntity
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    /**
     * Окно оф-пика в минутах от начала суток включительно.
     */
    public const DAY_MINUTES = 1440;

    public function __construct(
        #[ORM\Column(type: 'string', length: 64)]
        private string $code,
        #[ORM\Column(type: 'string', length: 255)]
        private string $name,
        #[ORM\Column(type: 'integer', nullable: true)]
        private ?int $offPeakStartMinute = null,
        #[ORM\Column(type: 'integer', nullable: true)]
        private ?int $offPeakEndMinute = null,
        #[ORM\Column(type: 'string', nullable: true)]
        private ?string $offPeakMultiplier = null,
    ) {
        $this->models = new ArrayCollection();
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setOffPeak(?int $startMinute, ?int $endMinute, ?string $multiplier): self
    {
        $this->offPeakStartMinute = $startMinute;
        $this->offPeakEndMinute = $endMinute;
        $this->offPeakMultiplier = $multiplier;

        return $this;
    }

    public function hasOffPeak(): bool
    {
        return $this->offPeakStartMinute !== null
            && $this->offPeakEndMinute !== null
            && $this->offPeakMultiplier !== null;
    }

    public function getOffPeakMultiplier(): ?string
    {
        return $this->offPeakMultiplier;
    }

    /**
     * Попадает ли время суток (минуты от начала суток UTC) в оф-пиковое окно
     * `[start, end)`; окно может переходить через полночь, включительно в `start`,
     * исключительно в `end`.
     */
    public function isOffPeak(int $minuteOfDay): bool
    {
        if (!$this->hasOffPeak()) {
            return false;
        }

        $start = (int) $this->offPeakStartMinute;
        $end = (int) $this->offPeakEndMinute;

        return $start <= $end
            ? $minuteOfDay >= $start && $minuteOfDay < $end
            : $minuteOfDay >= $start || $minuteOfDay < $end;
    }

    public function getModels(): Collection
    {
        return $this->models;
    }

    /**
     * @var Collection<int, Model>
     */
    #[ORM\OneToMany(mappedBy: 'provider', targetEntity: Model::class, cascade: ['remove'], orphanRemoval: true)]
    private Collection $models;
}
