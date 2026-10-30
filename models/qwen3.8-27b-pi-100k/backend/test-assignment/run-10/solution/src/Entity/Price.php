<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цены модели с данным `valid_from` (включительно).
 *
 * Денежные значения хранятся строками в 6 знаках (DECIMAL(20,6) в БД) —
 * тип float для денег не используется.
 *
 * Естественный ключ — (provider, model, valid_from).
 */
#[ORM\Entity]
#[ORM\Table(name: 'prices')]
#[ORM\Index(name: 'idx_price_lookup', columns: ['model_id', 'valid_from'])]
#[ORM\Index(name: 'idx_price_provider', columns: ['provider_id'])]
#[ORM\UniqueConstraint(name: 'uniq_price_model_valid_from', columns: ['model_id', 'valid_from'])]
class Price extends AbstractEntity
{
    public function __construct(
        #[ORM\ManyToOne(targetEntity: Provider::class)]
        #[ORM\JoinColumn(name: 'provider_id', nullable: false)]
        private Provider $provider,
        #[ORM\ManyToOne(targetEntity: Model::class, inversedBy: 'prices')]
        #[ORM\JoinColumn(name: 'model_id', nullable: false)]
        private Model $model,
        #[ORM\Column(type: 'datetime_immutable')]
        private \DateTimeImmutable $validFrom,
        #[ORM\Column(type: 'string', length: 8)]
        private string $currency,
        #[ORM\Column(type: 'string', length: 32)]
        private string $unit,
        #[ORM\Column(type: 'json')]
        private array $rates = [],
    ) {
        $this->unit = $unit;
        $this->rates = is_array($rates) ? $rates : json_decode($rates, true);
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

    public function getUnit(): string
    {
        return $this->unit;
    }

    /**
     * @var array<string, string>
     */
    public function getRates(): array
    {
        if (is_array($this->rates)) {
            return $this->rates;
        }

        return json_decode($this->rates, true);
    }

    /**
     * @param array<string, string>|string $rates
     */
    public function setUnitAndRates(string $unit, array|string $rates): self
    {
        $this->unit = $unit;
        $this->rates = is_array($rates) ? $rates : json_decode($rates, true);

        return $this;
    }
}
