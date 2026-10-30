<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prices', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_price', columns: ['provider_code', 'model_code', 'valid_from'])
])]
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

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit;

    #[ORM\Column(type: 'json')]
    private array $rates;

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
    public function getRates(): array { return $this->rates; }
    public function setRates(array $v): void { $this->rates = $v; }
}
