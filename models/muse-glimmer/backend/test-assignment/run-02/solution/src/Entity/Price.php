<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prices')]
#[ORM\UniqueConstraint(name: 'uniq_price_provider_model_valid', columns: ['provider_code', 'model_code', 'valid_from'])]
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

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(length: 8)]
    private string $currency;

    #[ORM\Column(length: 32)]
    private string $unit;

    #[ORM\Column(type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $inputRate = null;

    #[ORM\Column(type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $outputRate = null;

    #[ORM\Column(type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $cacheReadRate = null;

    #[ORM\Column(type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $cacheWriteRate = null;

    #[ORM\Column(type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $anyRate = null;

    public function getId(): ?int { return $this->id; }

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

    public function getInputRate(): ?string { return $this->inputRate; }
    public function setInputRate(?string $v): void { $this->inputRate = $v; }

    public function getOutputRate(): ?string { return $this->outputRate; }
    public function setOutputRate(?string $v): void { $this->outputRate = $v; }

    public function getCacheReadRate(): ?string { return $this->cacheReadRate; }
    public function setCacheReadRate(?string $v): void { $this->cacheReadRate = $v; }

    public function getCacheWriteRate(): ?string { return $this->cacheWriteRate; }
    public function setCacheWriteRate(?string $v): void { $this->cacheWriteRate = $v; }

    public function getAnyRate(): ?string { return $this->anyRate; }
    public function setAnyRate(?string $v): void { $this->anyRate = $v; }
}
