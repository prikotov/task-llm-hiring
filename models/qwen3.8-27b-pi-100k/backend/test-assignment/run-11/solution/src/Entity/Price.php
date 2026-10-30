<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цен модели, действующая с valid_from (включительно),
 * до следующей более поздней строки той же модели.
 * Естественный ключ (provider, model, valid_from).
 */
#[ORM\Entity]
#[ORM\Table(name: 'prices')]
#[ORM\UniqueConstraint(name: 'uq_prices_model_valid_from', columns: ['model', 'valid_from'])]
class Price
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    public const KIND_INPUT = 'input';
    public const KIND_OUTPUT = 'output';
    public const KIND_CACHE_READ = 'cache_read';
    public const KIND_CACHE_WRITE = 'cache_write';
    public const KIND_ANY = 'any';

    #[ORM\Id]
    #[ORM\Column]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model', referencedColumnName: 'id', onDelete: 'CASCADE')]
    private ?Model $model = null;

    /** UTC, moment, с которого строка цены действует (включительно). */
    #[ORM\Column(name: 'valid_from', type: Types::DATETIME_IMMUTABLE, nullable: false)]
    private ?\DateTimeImmutable $validFrom = null;

    #[ORM\Column(length: 3, nullable: false)]
    private string $currency = 'USD';

    #[ORM\Column(length: 32, nullable: false)]
    private string $unit = '';

    /**
     * @var array<string,string> строка -> десятичная строка (6 знаков)
     */
    #[ORM\Column(type: Types::JSON, nullable: false)]
    private array $rates = [];

    public function __construct(
        Model $model,
        \DateTimeImmutable $validFrom,
        string $currency,
        string $unit,
        array $rates,
    ) {
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }

    public function id(): ?int
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

    public function unit(): string
    {
        return $this->unit;
    }

    /** @return array<string,string> */
    public function rates(): array
    {
        return $this->rates;
    }

    /** @param array<string,string> $rates */
    public function replace(string $currency, string $unit, array $rates): void
    {
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }
}
