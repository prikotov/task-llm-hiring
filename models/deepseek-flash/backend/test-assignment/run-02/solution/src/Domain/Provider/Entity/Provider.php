<?php

declare(strict_types=1);

namespace App\Domain\Provider\Entity;

use App\Domain\Pricing\ValueObject\OffPeakWindow;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uniq_providers_code', columns: ['code'])]
final class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(name: 'off_peak_start', type: 'string', length: 5, nullable: true)]
    private ?string $offPeakStart = null;

    #[ORM\Column(name: 'off_peak_end', type: 'string', length: 5, nullable: true)]
    private ?string $offPeakEnd = null;

    #[ORM\Column(name: 'off_peak_multiplier', type: 'decimal', precision: 12, scale: 6, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
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

    public function rename(string $name): void
    {
        $this->name = $name;
    }

    public function offPeakWindow(): ?OffPeakWindow
    {
        if ($this->offPeakStart === null || $this->offPeakEnd === null || $this->offPeakMultiplier === null) {
            return null;
        }

        return new OffPeakWindow($this->offPeakStart, $this->offPeakEnd, $this->offPeakMultiplier);
    }

    public function changeOffPeak(?OffPeakWindow $window): void
    {
        if ($window === null) {
            $this->offPeakStart = null;
            $this->offPeakEnd = null;
            $this->offPeakMultiplier = null;

            return;
        }

        $this->offPeakStart = $window->start();
        $this->offPeakEnd = $window->end();
        $this->offPeakMultiplier = $window->multiplier();
    }
}
