<?php

declare(strict_types=1);

namespace App\Entity;

use App\Shared\ImportException;
use Doctrine\ORM\Mapping as ORM;

/**
 * An LLM provider (reference data). The off-peak window fields are stored as
 * plain "HH:MM" strings (UTC) + exact decimal multiplier; {@see \App\Domain\Pricing\OffPeak}
 * is the runtime view of them.
 */
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
class Provider
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string', length: 64)]
        private string $code,

        #[ORM\Column(type: 'string', length: 255)]
        private string $name,
    ) {
    }

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowStart = null;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakWindowEnd = null;

    #[ORM\Column(type: 'decimal', precision: 30, scale: 10, nullable: true)]
    private ?string $offPeakMultiplier = null;

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

    public function getOffPeakWindowStart(): ?string
    {
        return $this->offPeakWindowStart;
    }

    public function getOffPeakWindowEnd(): ?string
    {
        return $this->offPeakWindowEnd;
    }

    public function getOffPeakMultiplier(): ?string
    {
        return $this->offPeakMultiplier;
    }

    /**
     * @param string|null $windowStart "HH:MM" UTC or null (no discount)
     * @param string|null $windowEnd   "HH:MM" UTC or null
     * @param string|null $multiplier  positive decimal or null
     */
    public function setOffPeak(
        ?string $windowStart,
        ?string $windowEnd,
        ?string $multiplier,
    ): void {
        if (($windowStart === null) !== ($windowEnd === null)
            || ($windowStart === null) !== ($multiplier === null)) {
            throw new ImportException('off_peak requires window_start, window_end and multiplier together');
        }

        $this->offPeakWindowStart = $windowStart;
        $this->offPeakWindowEnd = $windowEnd;
        $this->offPeakMultiplier = $multiplier;
    }

    /** @return \App\Domain\Pricing\OffPeak|null */
    public function offPeak(): ?\App\Domain\Pricing\OffPeak
    {
        if ($this->offPeakWindowStart === null || $this->offPeakWindowEnd === null || $this->offPeakMultiplier === null) {
            return null;
        }

        return new \App\Domain\Pricing\OffPeak(
            $this->offPeakWindowStart,
            $this->offPeakWindowEnd,
            $this->offPeakMultiplier,
        );
    }
}
