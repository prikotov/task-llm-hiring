<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Версионированная строка цены модели.
 *
 * Естественный ключ — (model, valid_from). Цена действует с valid_from
 * включительно и до появления строки с большим valid_from для модели.
 * Цены хранятся decimal-строками (float для денег не используется нигде).
 */
#[ORM\Entity]
#[ORM\Table(
    name: 'price_rows',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uniq_price_rows_model_valid_from', columns: ['model_id', 'valid_from']),
    ],
)]
final class PriceRow
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_PER_TOKEN_UNIFIED = 'per_1m_tokens_unified';

    public const PER_TOKEN_RATE_KEYS = ['input', 'output', 'cache_read', 'cache_write'];
    public const UNIFIED_RATE_KEY = 'any';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private int $id;

    #[ORM\Column(name: 'model_id', type: Types::INTEGER)]
    private int $modelId;

    #[ORM\Column(name: 'valid_from', type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: Types::STRING, length: 3)]
    private string $currency;

    #[ORM\Column(type: Types::STRING, length: 32)]
    private string $unit;

    /** @var array<string, string> ключи зависят от unit (см. константы) */
    #[ORM\Column(type: Types::JSON)]
    private array $rates = [];

    public function __construct(
        int $modelId,
        \DateTimeImmutable $validFrom,
        string $currency,
        string $unit,
        array $rates,
    ) {
        $this->modelId = $modelId;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getModelId(): int
    {
        return $this->modelId;
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

    /** @return array<string, string> */
    public function getRates(): array
    {
        return $this->rates;
    }

    public function replaceRates(string $currency, string $unit, array $rates): void
    {
        $this->currency = $currency;
        $this->unit = $unit;
        $this->rates = $rates;
    }
}
