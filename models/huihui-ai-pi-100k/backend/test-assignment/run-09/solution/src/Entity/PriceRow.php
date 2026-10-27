<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\UnitRates;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка истории цен модели: действует с valid_from до появления строки
 * с большим valid_from. Естественный ключ (provider, model, valid_from).
 * Ставки хранятся decimal-строками (NUMERIC, не float).
 */
#[ORM\Entity]
#[ORM\Table(name: 'price_rows', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uq_price_rows', columns: ['provider_id', 'model_id', 'valid_from']),
])]
class PriceRow
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\Column(name: 'provider_id', type: 'integer')]
    private int $providerId = 0;

    #[ORM\Column(name: 'model_id', type: 'integer')]
    private int $modelId = 0;

    #[ORM\Column(name: 'valid_from', type: 'datetime_immutable')]
    private DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit = self::UNIT_PER_TOKEN;

    #[ORM\Column(type: 'string', length: 3)]
    private string $currency = 'USD';

    #[ORM\Column(name: 'rate_input', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateInput = null;

    #[ORM\Column(name: 'rate_output', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateOutput = null;

    #[ORM\Column(name: 'rate_cache_read', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateCacheRead = null;

    #[ORM\Column(name: 'rate_cache_write', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateCacheWrite = null;

    #[ORM\Column(name: 'rate_any', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $rateAny = null;

    public function __construct()
    {
        $this->validFrom = new DateTimeImmutable('2000-01-01T00:00:00+00:00');
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProviderId(): int
    {
        return $this->providerId;
    }

    public function setProviderId(int $providerId): void
    {
        $this->providerId = $providerId;
    }

    public function getModelId(): int
    {
        return $this->modelId;
    }

    public function setModelId(int $modelId): void
    {
        $this->modelId = $modelId;
    }

    public function getValidFrom(): DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function setValidFrom(DateTimeImmutable $validFrom): void
    {
        $this->validFrom = $validFrom;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): void
    {
        $this->currency = $currency;
    }

    public function applyRates(UnitRates $rates): void
    {
        $this->unit = $rates->unit;
        $this->rateInput = $rates->input;
        $this->rateOutput = $rates->output;
        $this->rateCacheRead = $rates->cacheRead;
        $this->rateCacheWrite = $rates->cacheWrite;
        $this->rateAny = $rates->any;
    }

    public function rates(): UnitRates
    {
        return new UnitRates(
            $this->unit,
            $this->rateInput,
            $this->rateOutput,
            $this->rateCacheRead,
            $this->rateCacheWrite,
            $this->rateAny,
        );
    }
}
