<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
class PriceRow
{
    public const UNIT_PER_1M = 'per_1m_tokens';
    public const UNIT_PER_1M_UNIFIED = 'per_1m_tokens_unified';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit;

    /**
     * @var array<string, string>
     */
    #[ORM\Column(type: 'json')]
    private array $rates = [];

    #[ORM\ManyToOne(targetEntity: Model::class, inversedBy: 'priceRows')]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    private Model $model;

    /**
     * @param array<string, string> $rates
     */
    public function __construct(
        \DateTimeImmutable $validFrom,
        string $currency,
        string $unit,
        array $rates,
        Model $model,
    ) {
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
        $this->model = $model;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * @return array<string, string>
     */
    public function getRates(): array
    {
        return $this->rates;
    }

    public function rate(string $kind): ?Money
    {
        return isset($this->rates[$kind]) ? Money::fromString($this->rates[$kind]) : null;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function setUnit(string $unit): void
    {
        $this->unit = $unit;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    /**
     * @param array<string, string> $rates
     */
    public function setRates(array $rates): void
    {
        $this->rates = $rates;
    }
}
