<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PriceRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цен модели: действует с valid_from, включает схему тарификации
 * и ставки. Естественный ключ — (provider, model, valid_from),
 * в схеме — (model, valid_from).
 */
#[ORM\Entity(repositoryClass: PriceRepository::class)]
#[ORM\Table(name: 'prices')]
#[ORM\UniqueConstraint(name: 'uq_prices_model_valid_from', columns: ['model_id', 'valid_from'])]
class Price
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', nullable: false, onDelete: 'CASCADE')]
    private Model $model;

    #[ORM\Column(name: 'valid_from', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: Types::STRING, length: 8)]
    private string $currency;

    #[ORM\Column(type: Types::STRING, length: 32)]
    private string $unit;

    /**
     * Ставки, десятичные строки:
     *  - per_1m_tokens:        input, output, cache_read, cache_write;
     *  - per_1m_tokens_unified: any.
     *
     * @var array<string, string>
     */
    #[ORM\Column(type: Types::JSON)]
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

    /**
     * @return array<string, string>
     */
    public function rates(): array
    {
        return $this->rates;
    }

    /**
     * Замена тарификационных параметров (ретро-коррекция/reprice);
     * естественный ключ (модель, valid_from) при этом не меняется.
     */
    public function update(string $currency, string $unit, array $rates): void
    {
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }
}
