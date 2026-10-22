<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Domain\Money\Decimal;
use App\Domain\Pricing\PriceRow;
use App\Domain\Pricing\PricingUnit;
use App\Domain\Pricing\Provider;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uniq_price_rows_model_valid_from', columns: ['model_id', 'valid_from'])]
#[ORM\Index(name: 'idx_price_rows_model', columns: ['model_id'])]
final class PriceRowEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: ModelEntity::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    private ModelEntity $model;

    #[ORM\Column(name: 'valid_from', type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency;

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit;

    /** @var array<string, string> ставки как десятичные строки; jsonb в БД */
    #[ORM\Column(type: Types::JSONB)]
    private array $rates;

    public function __construct(ModelEntity $model, DateTimeImmutable $validFrom, string $currency, string $unit, array $rates)
    {
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }

    public function applyFromDomain(PriceRow $row): void
    {
        $this->currency = $row->currency;
        $this->unit = $row->unit->value;
        $this->rates = array_map(
            static fn (Decimal $rate): string => $rate->fixed(6),
            $row->rates(),
        );
    }

    public function toDomain(Provider $provider): PriceRow
    {
        return new PriceRow(
            $provider,
            $this->model->toDomain(),
            $this->validFrom,
            PricingUnit::fromString($this->unit),
            $this->rates,
            $this->currency,
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function validFrom(): DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function ratesAsStrings(): array
    {
        return $this->rates;
    }

    public function unit(): string
    {
        return $this->unit;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function model(): ModelEntity
    {
        return $this->model;
    }
}
