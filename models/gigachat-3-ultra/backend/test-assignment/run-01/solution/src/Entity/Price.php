<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'prices')]
#[ORM\UniqueConstraint(name: 'uniq_price_provider_model_valid_from', columns: ['provider_code', 'model_id', 'valid_from'])]
class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'prices')]
    #[ORM\JoinColumn(name: 'provider_code', referencedColumnName: 'code', nullable: false, onDelete: 'CASCADE')]
    private Provider $provider;

    #[ORM\ManyToOne(targetEntity: Model::class, inversedBy: 'prices')]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Model $model;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 6)]
    private string $inputRate;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 6)]
    private string $outputRate;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 6)]
    private string $cacheReadRate;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 6)]
    private string $cacheWriteRate;

    #[ORM\Column(type: 'decimal', precision: 12, scale: 6, nullable: true)]
    private ?string $unifiedRate = null;

    public function __construct(
        Provider $provider,
        Model $model,
        \DateTimeImmutable $validFrom,
        string $currency,
        string $unit,
        string $inputRate,
        string $outputRate,
        string $cacheReadRate,
        string $cacheWriteRate,
        ?string $unifiedRate = null
    ) {
        $this->provider = $provider;
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->inputRate = $inputRate;
        $this->outputRate = $outputRate;
        $this->cacheReadRate = $cacheReadRate;
        $this->cacheWriteRate = $cacheWriteRate;
        $this->unifiedRate = $unifiedRate;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getModel(): Model
    {
        return $this->model;
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

    public function getInputRate(): string
    {
        return $this->inputRate;
    }

    public function getOutputRate(): string
    {
        return $this->outputRate;
    }

    public function getCacheReadRate(): string
    {
        return $this->cacheReadRate;
    }

    public function getCacheWriteRate(): string
    {
        return $this->cacheWriteRate;
    }

    public function getUnifiedRate(): ?string
    {
        return $this->unifiedRate;
    }
}