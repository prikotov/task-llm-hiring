<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prices')]
#[ORM\UniqueConstraint(columns: ['provider_code','model_code','valid_from'])]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id=null;

    #[ORM\Column(length:50)]
    private string $providerCode;

    #[ORM\Column(length:100)]
    private string $modelCode;

    #[ORM\Column(type:'datetimetz_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(length:3)]
    private string $currency;

    #[ORM\Column(length:50)]
    private string $unit;

    #[ORM\Column(type:'decimal', precision:20, scale:6, nullable:true)]
    private ?string $rateInput=null;

    #[ORM\Column(type:'decimal', precision:20, scale:6, nullable:true)]
    private ?string $rateOutput=null;

    #[ORM\Column(type:'decimal', precision:20, scale:6, nullable:true)]
    private ?string $rateCacheRead=null;

    #[ORM\Column(type:'decimal', precision:20, scale:6, nullable:true)]
    private ?string $rateCacheWrite=null;

    #[ORM\Column(type:'decimal', precision:20, scale:6, nullable:true)]
    private ?string $rateAny=null;

    public function __construct(string $providerCode, string $modelCode, \DateTimeImmutable $validFrom){
        $this->providerCode=$providerCode;
        $this->modelCode=$modelCode;
        $this->validFrom=$validFrom;
    }

    public function getProviderCode(): string { return $this->providerCode; }
    public function getModelCode(): string { return $this->modelCode; }
    public function getValidFrom(): \DateTimeImmutable { return $this->validFrom; }
    public function getCurrency(): string { return $this->currency; }
    public function setCurrency(string $c): void { $this->currency=$c; }
    public function getUnit(): string { return $this->unit; }
    public function setUnit(string $u): void { $this->unit=$u; }
    public function getRateInput(): ?string { return $this->rateInput; }
    public function setRateInput(?string $v): void { $this->rateInput=$v; }
    public function getRateOutput(): ?string { return $this->rateOutput; }
    public function setRateOutput(?string $v): void { $this->rateOutput=$v; }
    public function getRateCacheRead(): ?string { return $this->rateCacheRead; }
    public function setRateCacheRead(?string $v): void { $this->rateCacheRead=$v; }
    public function getRateCacheWrite(): ?string { return $this->rateCacheWrite; }
    public function setRateCacheWrite(?string $v): void { $this->rateCacheWrite=$v; }
    public function getRateAny(): ?string { return $this->rateAny; }
    public function setRateAny(?string $v): void { $this->rateAny=$v; }
}
