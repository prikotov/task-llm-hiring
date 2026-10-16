<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Decimal;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'price')]
#[ORM\UniqueConstraint('price_prov_model_vfrom_unique', ['provider_id', 'model_id', 'valid_from'])]
/**
 * Строка цены LLM-модели. Действовала с момента valid_from (включительно).
 * Разные строки для одной модели означают изменение тарифа со временем.
 */
final class Price
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private Provider $provider;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    private Model $model;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $valid_from;

    #[ORM\Column(type: 'string', length: 8)]
    private string $currency = 'USD';

    /**
     * per_1m_tokens: input, output, cache_read, cache_write
     * per_1m_tokens_unified: any
     *
     * @var array<string,string>
     */
    #[ORM\Column(type: 'json')]
    private array $rates;

    /**
     * @param array<string,string> $rates десятичные ставки (уже валидированы)
     */
    public function __construct(Provider $provider, Model $model, \DateTimeImmutable $valid_from, string $currency, array $rates)
    {
        if ($currency !== 'USD') {
            throw new \InvalidArgumentException("unsupported non-USD currency: " . $currency);
        }
        $this->provider = $provider;
        $this->model = $model;
        $this->valid_from = $valid_from;
        $this->currency = $currency;
        $this->rates = $rates;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKey(): string
    {
        return $this->provider->getCode() . '/' . $this->model->getCode() . '@' . $this->valid_from->format(\DateTimeInterface::ATOM);
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
        return $this->valid_from;
    }

    /**
     * Схему тарификации моделирует отдельная структура RateScheme — см. Rate.
     */
    public function isUnified(): bool
    {
        return array_key_exists('any', $this->rates);
    }

    public function getRates(): array
    {
        return $this->rates;
    }

    public function getRate(string $type): Decimal
    {
        if (!array_key_exists($type, $this->rates)) {
            throw new \RuntimeException("no rate for token type: $type");
        }
        return Decimal::fromString($this->rates[$type]);
    }
}
