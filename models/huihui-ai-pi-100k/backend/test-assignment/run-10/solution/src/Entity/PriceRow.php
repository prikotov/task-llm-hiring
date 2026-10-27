<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Billing\RateUnit;
use App\Repository\PriceRowRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Версия цены модели: действует с valid_from.
 *
 * Несколько версий одной модели — история цен. Естественный ключ —
 * (provider, model, valid_from). Тарифицируется строкой с наибольшим
 * valid_from <= ts события (valid_from включительно).
 *
 * Ставки хранятся JSON-объектом (decimal-строки) — гибко под любую схему
 * тарификации и без float для денег.
 */
#[ORM\Entity(repositoryClass: PriceRowRepository::class)]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uq_price_row', columns: ['provider_id', 'model_id', 'valid_from'])]
class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    private Provider $provider;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', onDelete: 'CASCADE', nullable: false)]
    private Model $model;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency = 'USD';

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit;

    /**
     * Ставки, decimal-строки:
     *   per_1m_tokens            => {input, output, cache_read, cache_write}
     *   per_1m_tokens_unified    => {any}
     *
     * @var array<string, string>
     */
    #[ORM\Column(type: Types::JSON)]
    private array $rates = [];

    public function __construct(
        Provider $provider,
        Model $model,
        \DateTimeImmutable $validFrom,
        string $unit,
        array $rates,
        ?string $currency = 'USD',
    ) {
        $this->provider = $provider;
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->unit = $unit;
        $this->rates = $rates;
        $this->currency = $currency ?? 'USD';
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function provider(): Provider
    {
        return $this->provider;
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

    public function unit(): RateUnit
    {
        return RateUnit::fromCode($this->unit);
    }

    /**
     * @return array<string, string>
     */
    public function rates(): array
    {
        return $this->rates;
    }

    /**
     * @param array<string, string> $rates
     */
    public function update(string $unit, array $rates, ?string $currency): void
    {
        $this->unit = $unit;
        $this->rates = $rates;
        $this->currency = $currency ?? 'USD';
    }
}
