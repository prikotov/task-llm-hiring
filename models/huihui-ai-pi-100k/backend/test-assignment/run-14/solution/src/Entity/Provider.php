<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\OffPeakWindow;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uq_providers_code', columns: ['code']),
])]
final class Provider
{
    public function __construct(
        #[ORM\Id]
        #[ORM\GeneratedValue]
        #[ORM\Column(type: 'integer')]
        private int|null $id = null,

        #[ORM\Column(type: 'string', length: 64)]
        public string $code = '',

        #[ORM\Column(type: 'string', length: 255)]
        public string $name = '',

        /** Nullable — провайдер может не иметь окна скидки. */
        #[ORM\Column(name: 'off_peak_start_min', type: 'integer', options: ['default' => 0])]
        public int $offPeakStartMinutes = 0,

        #[ORM\Column(name: 'off_peak_end_min', type: 'integer', options: ['default' => 0])]
        public int $offPeakEndMinutes = 0,

        /** Десятичная строка (bcmath), пример: "0.500000". */
        #[ORM\Column(name: 'off_peak_multiplier', type: 'string', length: 32)]
        public string $offPeakMultiplier = '1.000000',
    ) {
        $this->models = new ArrayCollection();
    }

    /** @var Collection<int, Model> */
    #[ORM\OneToMany(targetEntity: Model::class, mappedBy: 'provider')]
    private Collection $models;

    public function getModels(): Collection
    {
        return $this->models;
    }

    public function getOffPeak(): OffPeakWindow|null
    {
        return $this->hasOffPeak()
            ? new OffPeakWindow($this->offPeakStartMinutes, $this->offPeakEndMinutes, $this->offPeakMultiplier)
            : null;
    }

    /** true — у провайдера задано окно оф-пика. */
    #[ORM\Column(name: 'off_peak_enabled', type: 'boolean', options: ['default' => false])]
    public bool $hasOffPeak = false;

    public function hasOffPeak(): bool
    {
        return $this->hasOffPeak;
    }
}
