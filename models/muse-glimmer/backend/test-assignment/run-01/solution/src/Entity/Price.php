<?php

declare(strict_types=1);

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
    private int $id;

    #[ORM\Column(name: 'provider_code', type: 'string', length: 50)]
    private string $providerCode;

    #[ORM\Column(name: 'model_code', type: 'string', length: 50)]
    private string $modelCode;

    #[ORM\Column(name: 'valid_from', type: 'datetimetz_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 10)]
    private string $currency;

    #[ORM\Column(type: 'string', length: 50)]
    private string $unit;

    #[ORM\Column(name: 'rate_input', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateInput = null;

    #[ORM\Column(name: 'rate_output', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateOutput = null;

    #[ORM\Column(name: 'rate_cache_read', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateCacheRead = null;

    #[ORM\Column(name: 'rate_cache_write', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateCacheWrite = null;

    #[ORM\Column(name: 'rate_any', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateAny = null;

    public function __construct(
        string $providerCode,
        string $modelCode,
        \DateTimeImmutable $validFrom,
        string $currency,
        string $unit
    ) {
        $this->providerCode = $providerCode;
        $this->modelCode = $modelCode;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function getModelCode(): string
    {
        return $this->modelCode;
    }

    public function getValidFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getUnit(): string
    {
        return $this->unit;
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
