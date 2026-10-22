<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use App\Domain\Money\Decimal;
use App\Domain\Pricing\OffPeakWindow;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uniq_providers_code', columns: ['code'])]
final class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code;

    #[ORM\Column(type: 'string', length: 255)]
    private string $name;

    #[ORM\Column(name: 'off_peak_start', type: 'string', length: 5, nullable: true)]
    private ?string $offPeakStart = null;

    #[ORM\Column(name: 'off_peak_end', type: 'string', length: 5, nullable: true)]
    private ?string $offPeakEnd = null;

    #[ORM\Column(name: 'off_peak_multiplier', type: 'decimal', precision: 30, scale: 18, nullable: true)]
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

    public function updateDetails(string $name, ?OffPeakWindow $offPeak): bool
    {
        $changed = false;

        if ($this->name !== $name) {
            $this->name = $name;
            $changed = true;
        }

        $start = $offPeak?->windowStart();
        $end = $offPeak?->windowEnd();
        $multiplier = $offPeak?->multiplier()->format(18);

        if ($this->offPeakStart !== $start) {
            $this->offPeakStart = $start;
            $changed = true;
        }

        if ($this->offPeakEnd !== $end) {
            $this->offPeakEnd = $end;
            $changed = true;
        }

        if (!$this->sameDecimal($this->offPeakMultiplier, $multiplier)) {
            $this->offPeakMultiplier = $multiplier;
            $changed = true;
        }

        return $changed;
    }

    public function offPeakWindow(): ?OffPeakWindow
    {
        if ($this->offPeakStart === null || $this->offPeakEnd === null || $this->offPeakMultiplier === null) {
            return null;
        }

        return OffPeakWindow::fromStrings($this->offPeakStart, $this->offPeakEnd, $this->offPeakMultiplier);
    }

    private function sameDecimal(?string $left, ?string $right): bool
    {
        if ($left === null || $right === null) {
            return $left === $right;
        }

        return Decimal::fromString($left)->equals(Decimal::fromString($right));
    }
}
