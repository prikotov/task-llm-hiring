<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prices', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_provider_model_valid', columns: ['provider_code', 'model_code', 'valid_from'])
])]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $providerCode;

    #[ORM\Column(length: 64)]
    private string $modelCode;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(length: 3)]
    private string $currency;

    #[ORM\Column(length: 32)]
    private string $unit;

    #[ORM\Column(type: 'json')]
    private array $rates;

    public function __construct(
        string $providerCode,
        string $modelCode,
        \DateTimeImmutable $validFrom,
        string $currency,
        string $unit,
        array $rates
    ) {
        $this->providerCode = $providerCode;
        $this->modelCode = $modelCode;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }

    public function getId(): ?int { return $this->id; }
    public function getProviderCode(): string { return $this->providerCode; }
    public function getModelCode(): string { return $this->modelCode; }
    public function getValidFrom(): \DateTimeImmutable { return $this->validFrom; }
    public function getCurrency(): string { return $this->currency; }
    public function getUnit(): string { return $this->unit; }
    public function getRates(): array { return $this->rates; }
}
