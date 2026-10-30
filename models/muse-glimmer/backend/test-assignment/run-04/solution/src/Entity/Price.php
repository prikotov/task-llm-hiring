<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prices')]
#[ORM\UniqueConstraint(name: 'uniq_price', columns: ['provider_code', 'model_code', 'valid_from'])]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $providerCode;

    #[ORM\Column(type: 'string', length: 64)]
    private string $modelCode;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    private ?string $rateInput = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    private ?string $rateOutput = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    private ?string $rateCacheRead = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    private ?string $rateCacheWrite = null;

    #[ORM\Column(type: 'decimal', precision: 18, scale: 6, nullable: true)]
    private ?string $rateAny = null;

    public function getProviderCode(): string { return $this->providerCode; }
    public function setProviderCode(string $v): void { $this->providerCode = $v; }
    public function getModelCode(): string { return $this->modelCode; }
    public function setModelCode(string $v): void { $this->modelCode = $v; }
    public function getValidFrom(): \DateTimeImmutable { return $this->validFrom; }
    public function setValidFrom(\DateTimeImmutable $v): void { $this->validFrom = $v; }
    public function getCurrency(): string { return $this->currency; }
    public function setCurrency(string $v): void { $this->currency = $v; }
    public function getUnit(): string { return $this->unit; }
    public function setUnit(string $v): void { $this->unit = $v; }
    public function getRateInput(): ?string { return $this->rateInput; }
    public function setRateInput(?string $v): void { $this->rateInput = $v; }
    public function getRateOutput(): ?string { return $this->rateOutput; }
    public function setRateOutput(?string $v): void { $this->rateOutput = $v; }
    public function getRateCacheRead(): ?string { return $this->rateCacheRead; }
    public function setRateCacheRead(?string $v): void { $this->rateCacheRead = $v; }
    public function getRateCacheWrite(): ?string { return $this->rateCacheWrite; }
    public function setRateCacheWrite(?string $v): void { $this->rateCacheWrite = $v; }
    public function getRateAny(): ?string { return $this->rateAny; }
    public function setRateAny(?string $v): void { $this->rateAny = $v; }
}
