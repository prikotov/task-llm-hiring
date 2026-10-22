<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Domain\Money\Decimal;
use App\Domain\Pricing\OffPeakWindow;
use App\Domain\Pricing\Provider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'providers')]
#[ORM\UniqueConstraint(name: 'uniq_providers_code', columns: ['code'])]
final class ProviderEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $code;

    #[ORM\Column(type: 'string', length: 128)]
    private string $name;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakStart = null;

    #[ORM\Column(type: 'string', length: 5, nullable: true)]
    private ?string $offPeakEnd = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    private ?string $offPeakMultiplier = null;

    public function __construct(string $code, string $name)
    {
        $this->code = $code;
        $this->name = $name;
    }

    public static function fromDomain(Provider $provider): self
    {
        $entity = new self($provider->code, $provider->name);
        $entity->applyOffPeak($provider->offPeak);

        return $entity;
    }

    public function applyFromDomain(Provider $provider): void
    {
        $this->name = $provider->name;
        $this->applyOffPeak($provider->offPeak);
    }

    public function toDomain(): Provider
    {
        $offPeak = null;
        if (null !== $this->offPeakStart && null !== $this->offPeakEnd && null !== $this->offPeakMultiplier) {
            $offPeak = OffPeakWindow::fromStrings($this->offPeakStart, $this->offPeakEnd, $this->offPeakMultiplier);
        }

        return new Provider($this->code, $this->name, $offPeak, $this->id);
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function code(): string
    {
        return $this->code;
    }

    private function applyOffPeak(?OffPeakWindow $offPeak): void
    {
        if (null === $offPeak) {
            $this->offPeakStart = null;
            $this->offPeakEnd = null;
            $this->offPeakMultiplier = null;

            return;
        }

        $this->offPeakStart = $offPeak->windowStartLabel();
        $this->offPeakEnd = $offPeak->windowEndLabel();
        $this->offPeakMultiplier = $offPeak->multiplier->fixed(6);
    }
}
