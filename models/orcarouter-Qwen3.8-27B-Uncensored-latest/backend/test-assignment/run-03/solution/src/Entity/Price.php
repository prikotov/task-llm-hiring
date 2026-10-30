<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\PriceRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PriceRepository::class)]
#[ORM\Table(name: 'price_rows')]
#[ORM\UniqueConstraint(name: 'uniq_price_rows', columns: ['provider_id', 'model_id', 'valid_from'])]
final class Price
{
    public const UNIT_PER_TOKEN = 'per_1m_tokens';
    public const UNIT_PER_TOKEN_UNIFIED = 'per_1m_tokens_unified';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private Provider $provider;

    #[ORM\ManyToOne(targetEntity: LlmModel::class, inversedBy: 'prices')]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    private LlmModel $model;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 8)]
    private string $currency = 'USD';

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit;

    /**
     * Ставки как JSON-объект десятичных строк, напр.:
     * {"input":"3.000000","output":"12.000000"} или {"any":"2.400000"}.
     */
    #[ORM\Column(type: 'text')]
    private string $ratesJson;

    public function getId(): int
    {
        return $this->id;
    }

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function setProvider(Provider $provider): self
    {
        $this->provider = $provider;

        return $this;
    }

    public function getModel(): LlmModel
    {
        return $this->model;
    }

    public function setModel(LlmModel $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getValidFrom(): DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function setValidFrom(DateTimeImmutable $validFrom): self
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getRatesJson(): string
    {
        return $this->ratesJson;
    }

    /**
     * @param array<string, string> $rates
     */
    public function setRates(string $unit, array $rates): self
    {
        $this->unit = $unit;

        $json = json_encode($rates, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        $this->ratesJson = $json;

        return $this;
    }

    /** @return array<string, string> */
    public function getRates(): array
    {
        $decoded = json_decode($this->ratesJson, true, 512, JSON_THROW_ON_ERROR);

        return is_array($decoded) ? $decoded : [];
    }
}
