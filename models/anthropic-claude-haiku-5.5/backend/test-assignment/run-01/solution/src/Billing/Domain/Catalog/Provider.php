<?php

declare(strict_types=1);

namespace App\Billing\Domain\Catalog;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'billing_provider')]
class Provider
{
    #[ORM\Id]
    #[ORM\Column(length: 64)]
    private string $code;

    #[ORM\Column(length: 255)]
    private string $name;

    #[ORM\Column(name: 'off_peak_start', length: 5, nullable: true)]
    private ?string $offPeakStart = null;

    #[ORM\Column(name: 'off_peak_end', length: 5, nullable: true)]
    private ?string $offPeakEnd = null;

    #[ORM\Column(name: 'off_peak_multiplier', type: 'decimal', precision: 12, scale: 6, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function __construct(string $code, string $name, ?OffPeakWindow $offPeak)
    {
        $this->code = $code;
        $this->changeDetails($name, $offPeak);
    }

    public function changeDetails(string $name, ?OffPeakWindow $offPeak): void
    {
        $this->name = $name;
        $this->offPeakStart = $offPeak?->start;
        $this->offPeakEnd = $offPeak?->end;
        $this->offPeakMultiplier = $offPeak?->multiplierValue();
    }

    public function code(): string
    {
        return $this->code;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function offPeak(): ?OffPeakWindow
    {
        if ($this->offPeakStart === null || $this->offPeakEnd === null || $this->offPeakMultiplier === null) {
            return null;
        }

        return new OffPeakWindow($this->offPeakStart, $this->offPeakEnd, $this->offPeakMultiplier);
    }
}
