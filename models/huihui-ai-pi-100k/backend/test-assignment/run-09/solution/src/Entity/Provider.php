<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\OffPeakPolicy;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uq_providers_code', columns: ['code']),
])]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\Column(type: 'string')]
    private string $code = '';

    #[ORM\Column(type: 'string')]
    private string $name = '';

    #[ORM\Column(name: 'off_peak_start_min', type: 'integer', nullable: true)]
    private ?int $offPeakStartMin = null;

    #[ORM\Column(name: 'off_peak_end_min', type: 'integer', nullable: true)]
    private ?int $offPeakEndMin = null;

    #[ORM\Column(name: 'off_peak_multiplier', type: 'string', nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function clearOffPeak(): void
    {
        $this->offPeakStartMin = null;
        $this->offPeakEndMin = null;
        $this->offPeakMultiplier = null;
    }

    /**
     * Окно храним в минутах суток; множитель — decimal-строкой (не float).
     */
    public function setOffPeak(int $startMin, int $endMin, string $multiplier): void
    {
        $this->offPeakStartMin = $startMin;
        $this->offPeakEndMin = $endMin;
        $this->offPeakMultiplier = $multiplier;
    }

    public function offPeak(): ?OffPeakPolicy
    {
        if ($this->offPeakStartMin === null || $this->offPeakEndMin === null || $this->offPeakMultiplier === null) {
            return null;
        }

        return OffPeakPolicy::fromMinutes(
            $this->offPeakStartMin,
            $this->offPeakEndMin,
            $this->offPeakMultiplier,
        );
    }
}
