<?php

declare(strict_types=1);

namespace App\Domain\Catalog;

use App\Domain\ValueObject\OffPeak;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uniq_providers_code', columns: ['code'])]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 128)]
    private string $code = '';

    #[ORM\Column(type: 'string', length: 255)]
    private string $name = '';

    #[ORM\Column(name: 'off_peak_start', type: 'string', length: 5, nullable: true)]
    private ?string $offPeakStart = null;

    #[ORM\Column(name: 'off_peak_end', type: 'string', length: 5, nullable: true)]
    private ?string $offPeakEnd = null;

    #[ORM\Column(name: 'off_peak_multiplier', type: 'string', length: 32, nullable: true)]
    private ?string $offPeakMultiplier = null;

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

    public function offPeak(): ?OffPeak
    {
        if ($this->offPeakStart === null || $this->offPeakEnd === null || $this->offPeakMultiplier === null) {
            return null;
        }

        return new OffPeak($this->offPeakStart, $this->offPeakEnd, $this->offPeakMultiplier);
    }

    public function setCode(string $code): void
    {
        $this->code = $code;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setOffPeak(?OffPeak $offPeak): void
    {
        if ($offPeak === null) {
            $this->offPeakStart = null;
            $this->offPeakEnd = null;
            $this->offPeakMultiplier = null;

            return;
        }

        $this->offPeakStart = $offPeak->windowStart();
        $this->offPeakEnd = $offPeak->windowEnd();
        $this->offPeakMultiplier = $offPeak->multiplier();
    }
}
