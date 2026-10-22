<?php

declare(strict_types=1);

namespace App\Domain\Model;

use App\Domain\Provider\Provider;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'price_line')]
#[ORM\UniqueConstraint(name: 'uniq_price_line', columns: ['provider', 'model', 'valid_from'])]
class PriceLine
{
    public const UNIT_TOKENS = 'per_1m_tokens';
    public const UNIT_UNIFIED = 'per_1m_tokens_unified';

    public const RATES_UNIT = '1000000';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class, inversedBy: 'priceLines')]
    #[ORM\JoinColumn(name: 'provider', referencedColumnName: 'id', nullable: false)]
    private Provider $provider;

    #[ORM\ManyToOne(targetEntity: Model::class, inversedBy: 'priceLines')]
    #[ORM\JoinColumn(name: 'model', referencedColumnName: 'id', nullable: false)]
    private Model $model;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 8)]
    private string $currency = 'USD';

    #[ORM\Column(type: 'string', length: 32)]
    private string $unit = self::UNIT_TOKENS;

    /** @var string JSON со схемой ставок (десятичные строки) */
    #[ORM\Column(type: 'text')]
    private string $ratesJson = '{}';

    public function __construct()
    {
        $this->validFrom = new \DateTimeImmutable('2000-01-01 00:00:00', new \DateTimeZone('UTC'));
    }

    public function getId(): ?int
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

    public function getModel(): Model
    {
        return $this->model;
    }

    public function setModel(Model $model): self
    {
        $this->model = $model;

        return $this;
    }

    public function getValidFrom(): \DateTimeImmutable
    {
        return $this->validFrom;
    }

    public function setValidFrom(\DateTimeImmutable $validFrom): self
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

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function getRatesJson(): string
    {
        return $this->ratesJson;
    }

    public function setRatesJson(string $ratesJson): self
    {
        $this->ratesJson = $ratesJson;

        return $this;
    }

    /** @return array<string,string> ставки с decimal-строками */
    public function getRates(): array
    {
        $decoded = json_decode($this->ratesJson, true);

        return is_array($decoded) ? $decoded : [];
    }
}
