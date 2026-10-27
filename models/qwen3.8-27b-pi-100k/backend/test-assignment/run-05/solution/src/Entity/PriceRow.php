<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'price_row')]
#[ORM\UniqueConstraint(name: 'uniq_price_row_model_valid_from', columns: ['model_id', 'valid_from'])]
class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Model $model;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 8)]
    private string $currency = 'USD';

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit = '';

    /** Десятичная строка, 6 знаков; null, если схема не per_1m_tokens. */
    #[ORM\Column(type: 'decimal', precision: 16, scale: 6, nullable: true)]
    private ?string $rateInput = null;

    #[ORM\Column(type: 'decimal', precision: 16, scale: 6, nullable: true)]
    private ?string $rateOutput = null;

    #[ORM\Column(type: 'decimal', precision: 16, scale: 6, nullable: true)]
    private ?string $rateCacheRead = null;

    #[ORM\Column(type: 'decimal', precision: 16, scale: 6, nullable: true)]
    private ?string $rateCacheWrite = null;

    /** Единая цена, схема per_1m_tokens_unified. */
    #[ORM\Column(type: 'decimal', precision: 16, scale: 6, nullable: true)]
    private ?string $rateAny = null;

    public function __construct(Model $model, DateTimeImmutable $validFrom)
    {
        $this->model = $model;
        $this->validFrom = $validFrom;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getValidFrom(): DateTimeImmutable
    {
        return $this->validFrom;
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

    public function getRateInput(): ?string
    {
        return $this->rateInput;
    }

    public function getRateOutput(): ?string
    {
        return $this->rateOutput;
    }

    public function getRateCacheRead(): ?string
    {
        return $this->rateCacheRead;
    }

    public function getRateCacheWrite(): ?string
    {
        return $this->rateCacheWrite;
    }

    public function getRateAny(): ?string
    {
        return $this->rateAny;
    }

    public function applyUnitAndRates(string $unit, ?string $rateInput, ?string $rateOutput, ?string $rateCacheRead, ?string $rateCacheWrite, ?string $rateAny): void
    {
        $this->unit = $unit;
        $this->rateInput = $rateInput;
        $this->rateOutput = $rateOutput;
        $this->rateCacheRead = $rateCacheRead;
        $this->rateCacheWrite = $rateCacheWrite;
        $this->rateAny = $rateAny;
    }
}
