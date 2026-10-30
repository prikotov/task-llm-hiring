<?php

declare(strict_types=1);

namespace App\Entity\Billing;

use App\Domain\Billing\PriceUnit;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uniq_price_rows_model_valid_from', columns: ['model_id', 'valid_from'])]
#[ORM\Index(name: 'idx_price_rows_model_valid_from', columns: ['model_id', 'valid_from'])]
final class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', nullable: false, onDelete: 'CASCADE')]
    private Model $model;

    /** Момент вступления строки в силу (UTC), граница включительно. */
    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 8)]
    private string $currency = 'USD';

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit = PriceUnit::Per1MToken->value;

    /**
     * Ставки — JSON с десятичными строками (без float):
     *  per_1m_tokens:          {"input": "3.000000", "output": ..., "cache_read": ..., "cache_write": ...}
     *  per_1m_tokens_unified:  {"any": "2.400000"}
     *
     * @var array<string, string>
     */
    #[ORM\Column(type: 'json')]
    private array $rates = [];

    public function __construct(Model $model, \DateTimeImmutable $validFrom)
    {
        $this->model = $model;
        $this->validFrom = $validFrom;
    }

    /**
     * @param array<string, string> $rates
     */
    public function setQuote(PriceUnit $unit, array $rates, string $currency = 'USD'): void
    {
        $this->unit = $unit->value;
        $this->rates = $rates;
        $this->currency = $currency;
    }

    public function id(): int
    {
        return $this->id;
    }

    public function model(): Model
    {
        return $this->model;
    }

    public function validFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function currency(): string
    {
        return $this->currency;
    }

    public function unit(): PriceUnit
    {
        return PriceUnit::fromString($this->unit);
    }

    /**
     * @return array<string, string>
     */
    public function rates(): array
    {
        return $this->rates;
    }
}
