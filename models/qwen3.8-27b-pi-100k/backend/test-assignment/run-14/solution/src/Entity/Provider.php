<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Провайдер с опциональным окном оф-пик (UTC-время суток).
 * Natural key: code.
 */
#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'ux_providers_code', columns: ['code'])]
class Provider
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int|null $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code = '';

    #[ORM\Column(type: 'string', length: 191)]
    private string $name = '';

    // off_peak: три строки (HH:MM, HH:MM, decimal) или все null
    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private string|null $offPeakWindowStart = null;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private string|null $offPeakWindowEnd = null;

    #[ORM\Column(type: 'string', length: 24, nullable: true)]
    private string|null $offPeakMultiplier = null;

    public function id(): ?int
    {
        return $this->id;
    }

    public function isNew(): bool
    {
        return $this->id === null;
    }

    public function withIdentity(string $code, string $name): void
    {
        $this->code = $code;
        $this->name = $name;
    }

    public function code(): string
    {
        return $this->code;
    }

    public function name(): string
    {
        return $this->name;
    }

    /**
     * Устанавливает или очищает оф-пик провайдера.
     */
    public function setOffPeak(?string $windowStart, ?string $windowEnd, ?string $multiplier): void
    {
        $this->offPeakWindowStart = $windowStart;
        $this->offPeakWindowEnd = $windowEnd;
        $this->offPeakMultiplier = $multiplier;
    }

    public function hasOffPeak(): bool
    {
        return $this->offPeakWindowStart !== null
            && $this->offPeakWindowEnd !== null
            && $this->offPeakMultiplier !== null;
    }

    public function offPeak(): ?\App\Domain\Pricing\OffPeakWindow
    {
        if (!$this->hasOffPeak()) {
            return null;
        }

        return new \App\Domain\Pricing\OffPeakWindow(
            (string) $this->offPeakWindowStart,
            (string) $this->offPeakWindowEnd,
            (string) $this->offPeakMultiplier,
        );
    }
}
