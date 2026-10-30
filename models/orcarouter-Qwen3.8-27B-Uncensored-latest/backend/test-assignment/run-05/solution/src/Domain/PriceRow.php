<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цен: действует для модели начиная с момента valid_from (включительно).
 *
 * У одной модели может быть несколько строк с разными valid_from; при тарификации
 * берётся строка с наибольшим valid_from, не превышающим момент события.
 */
#[ORM\Entity]
#[ORM\Table(name: 'billing_price_row')]
#[ORM\UniqueConstraint(
    name: 'uniq_billing_price_row_provider_model_valid_from',
    columns: ['provider_id', 'model_id', 'valid_from'],
)]
class PriceRow
{
    public function __construct(
        #[ORM\Id, ORM\GeneratedValue, ORM\Column(type: 'integer')]
        private ?int $id,

        #[ORM\ManyToOne(targetEntity: Provider::class)]
        #[ORM\JoinColumn(name: 'provider_id', nullable: false, onDelete: 'CASCADE')]
        private Provider $provider,

        #[ORM\ManyToOne(targetEntity: Model::class)]
        #[ORM\JoinColumn(name: 'model_id', nullable: false, onDelete: 'CASCADE')]
        private Model $model,

        #[ORM\Column(type: 'timestamptz')]
        private \DateTimeImmutable $validFrom,

        // Поле зарезервировано на будущее (на данный момент только USD).
        #[ORM\Column(type: 'string', length: 3)]
        private string $currency = 'USD',

        #[ORM\Column(type: 'string', enumType: BillingUnit::class)]
        private BillingUnit $unit = BillingUnit::PerToken,

        /**
         * Ставки десятичными строками; ключи зависят от схемы
         * (input/output/cache_read/cache_write или any).
         * Хранится как JSON-объект; значения — строки (float для денег не используется).
         *
         * @var array<string, string>
         */
        #[ORM\Column(type: 'json')]
        private array $rates = [],
    ) {
    }

    public static function make(Provider $provider, Model $model, \DateTimeImmutable $validFrom): self
    {
        return new self(id: null, provider: $provider, model: $model, validFrom: $validFrom);
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

    public function getValidFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function getUnit(): BillingUnit
    {
        return $this->unit;
    }

    /** @return array<string, string> */
    public function getRates(): array
    {
        return $this->rates;
    }

    /** @param array<string, string> $rates */
    public function update(\DateTimeImmutable $validFrom, string $currency, BillingUnit $unit, array $rates): void
    {
        $this->unit = $unit;
        $this->validFrom = $validFrom;
        $this->currency = $currency;
        $this->rates = $rates;
    }
}
