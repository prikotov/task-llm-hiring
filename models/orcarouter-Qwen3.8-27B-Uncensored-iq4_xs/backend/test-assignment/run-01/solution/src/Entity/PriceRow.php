<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка истории цен: цены модели, действующие с момента valid_from.
 * Уникальна по связке (provider, model, valid_from).
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uniq_price_row', columns: ['provider_id', 'model_id', 'valid_from'])]
#[ORM\Index(name: 'idx_price_rows_lookup', columns: ['provider_id', 'model_id', 'valid_from'])]
class PriceRow
{
    /** Схемы тарификации. */
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    /** Виды токенов для схемы per_1m_tokens. */
    public const TOKEN_KINDS = ['input', 'output', 'cache_read', 'cache_write'];
    public const UNIFIED_RATE_KEY = 'any';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private Provider $provider;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    private Model $model;

    /** Момент, с которого цены действительны (включительно). */
    #[ORM\Column(name: 'valid_from', type: 'datetime_immutable')]
    private DateTimeImmutable $validFrom;

    /** Зарезервировано на будущее; на текущий момент всегда USD. */
    #[ORM\Column(type: 'string', length: 8)]
    private string $currency;

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit;

    /**
     * Тарифы как десятичные строки: per_1m_tokens — ключи TOKEN_KINDS,
     * per_1m_tokens_unified — ключ 'any'. Строки сохраняются как есть,
     * все вычисления — bcmath.
     *
     * @var array<string, string>
     */
    #[ORM\Column(type: 'json')]
    private array $rates = [];

    public function __construct(
        Provider $provider,
        Model $model,
        DateTimeImmutable $validFrom,
        string $currency,
        string $unit,
        array $rates,
    ) {
        $this->provider = $provider;
        $this->model = $model;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }

    public function getId(): ?int
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

    public function getValidFrom(): DateTimeImmutable
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

    /** @param array<string, string> $rates */
    public function setRates(array $rates): void
    {
        $this->rates = $rates;
    }

    public function apply(
        string $currency,
        string $unit,
        array $rates,
    ): void {
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }

    /** @return array<string, string> */
    public function getRates(): array
    {
        return $this->rates;
    }
}
