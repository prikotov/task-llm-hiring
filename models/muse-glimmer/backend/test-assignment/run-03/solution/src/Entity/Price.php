<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prices')]
#[ORM\UniqueConstraint(name: 'uniq_price', columns: ['provider_code', 'model_code', 'valid_from'])]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $providerCode;

    #[ORM\Column(length: 64)]
    private string $modelCode;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(length: 32)]
    private string $unit;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 6, nullable: true)]
    private ?string $rateInput = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 6, nullable: true)]
    private ?string $rateOutput = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 6, nullable: true)]
    private ?string $rateCacheRead = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 6, nullable: true)]
    private ?string $rateCacheWrite = null;

    #[ORM\Column(type: 'decimal', precision: 10, scale: 6, nullable: true)]
    private ?string $rateAny = null;

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function setProviderCode(string $providerCode): void
    {
        $this->providerCode = $providerCode;
    }

    public function getModelCode(): string
    {
        return $this->modelCode;
    }

    public function setModelCode(string $modelCode): void
    {
        $this->modelCode = $modelCode;
    }

    public function getValidFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function setValidFrom(\DateTimeImmutable $validFrom): void
    {
        $this->validFrom = $validFrom;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    public function getRateInput(): ?string
    {
        return $this->rateInput;
    }

    public function setRateInput(?string $rateInput): void
    {
        $this->rateInput = $rateInput;
    }

    public function getRateOutput(): ?string
    {
        return $this->rateOutput;
    }

    public function setRateOutput(?string $rateOutput): void
    {
        $this->rateOutput = $rateOutput;
    }

    public function getRateCacheRead(): ?string
    {
        return $this->rateCacheRead;
    }

    public function setRateCacheRead(?string $rateCacheRead): void
    {
        $this->rateCacheRead = $rateCacheRead;
    }

    public function getRateCacheWrite(): ?string
    {
        return $this->rateCacheWrite;
    }

    public function setRateCacheWrite(?string $rateCacheWrite): void
    {
        $this->rateCacheWrite = $rateCacheWrite;
    }

    public function getRateAny(): ?string
    {
        return $this->rateAny;
    }

    public function setRateAny(?string $rateAny): void
    {
        $this->rateAny = $rateAny;
    }
}
