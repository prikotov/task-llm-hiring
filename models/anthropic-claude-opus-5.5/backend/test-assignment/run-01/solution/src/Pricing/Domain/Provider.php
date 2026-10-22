<?php

declare(strict_types=1);

namespace App\Pricing\Domain;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'provider')]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64, unique: true)]
    private string $code;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $offPeakStartMinute = null;

    #[ORM\Column(type: Types::SMALLINT, nullable: true)]
    private ?int $offPeakEndMinute = null;

    #[ORM\Column(type: Types::DECIMAL, precision: 20, scale: 6, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function __construct(string $code, string $name, ?OffPeakWindow $offPeak)
    {
        $this->code = $code;
        $this->update($name, $offPeak);
    }

    public function update(string $name, ?OffPeakWindow $offPeak): void
    {
        $this->name = $name;
        $this->offPeakStartMinute = $offPeak?->startMinute;
        $this->offPeakEndMinute = $offPeak?->endMinute;
        $this->offPeakMultiplier = $offPeak?->multiplier->value;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function offPeak(): ?OffPeakWindow
    {
        if ($this->offPeakMultiplier === null || $this->offPeakStartMinute === null || $this->offPeakEndMinute === null) {
            return null;
        }

        return OffPeakWindow::fromStored($this->offPeakStartMinute, $this->offPeakEndMinute, $this->offPeakMultiplier);
    }
}
