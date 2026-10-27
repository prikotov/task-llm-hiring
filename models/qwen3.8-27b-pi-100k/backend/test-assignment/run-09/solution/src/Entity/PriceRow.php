<?php

declare(strict_types=1);

namespace App\Entity;

use App\Service\BillingRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Строка цены модели, действующая с момента `valid_from`.
 *
 * Естественный ключ — (provider, model, valid_from). Ставки — child-строки
 * (PriceRate), по одной на вид токена (input/output/cache_read/cache_write/any).
 */
#[ORM\Entity(repositoryClass: BillingRepository::class)]
#[ORM\UniqueConstraint(name: 'uniq_price_row_provider_model_valid_from', columns: ['provider_id', 'model_id', 'valid_from'])]
#[ORM\Index(name: 'idx_price_row_provider', fields: ['provider'])]
#[ORM\Index(name: 'idx_price_row_model', fields: ['model'])]
class PriceRow
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private Provider $provider;

    #[ORM\ManyToOne(targetEntity: TokenModel::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    private TokenModel $model;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $validFrom;

    #[ORM\Column(type: 'string', length: 8)]
    private string $currency = 'USD';

    /** per_1m_tokens | per_1m_tokens_unified */
    #[ORM\Column(type: 'string', length: 32)]
    private string $unit;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $createdAt;

    #[ORM\OneToMany(mappedBy: 'priceRow', targetEntity: PriceRate::class, cascade: ['persist', 'remove'], orphanRemoval: true)]
    #[ORM\OrderBy(['id' => 'ASC'])]
    private Collection $rates;

    public function __construct()
    {
        $this->rates = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
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

    public function getModel(): TokenModel
    {
        return $this->model;
    }

    public function setModel(TokenModel $model): self
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

    /**
     * Ставка для вида токена (десятичная строка) либо null, если её нет.
     */
    public function rateFor(string $kind): ?string
    {
        foreach ($this->rates as $rate) {
            if ($rate->getKind() === $kind) {
                return $rate->getRate();
            }
        }

        return null;
    }

    /**
     * Замещает набор ставок строки цен списком [kind => decimal].
     *
     * @param array<string, string> $rates
     */
    public function replaceRates(array $rates): self
    {
        $desiredKinds = array_keys($rates);

        $existingByKind = [];
        foreach ($this->rates as $rate) {
            $existingByKind[$rate->getKind()] = $rate;
        }

        // Удаляем виды, которых больше нет в наборе (orphan removal).
        foreach ($existingByKind as $kind => $rate) {
            if (!\in_array($kind, $desiredKinds, true)) {
                $this->rates->removeElement($rate);
            }
        }

        // Обновляем существующие по виду на месте; недостающие — добавляем.
        // Обновление на месте (без delete+insert) обходит нарушение уникального
        // ограничения (price_row_id, kind) при повторном импорте/коррекции.
        foreach ($rates as $kind => $rate) {
            if (isset($existingByKind[$kind])) {
                $existingByKind[$kind]->setRate($rate);
            } else {
                $priceRate = new PriceRate();
                $priceRate->setKind($kind);
                $priceRate->setRate($rate);
                $this->addRate($priceRate);
            }
        }

        return $this;
    }

    /** @return Collection<int, PriceRate> */
    public function getRates(): Collection
    {
        return $this->rates;
    }

    public function addRate(PriceRate $priceRate): self
    {
        if (!$this->rates->contains($priceRate)) {
            $this->rates->add($priceRate);
            $priceRate->setPriceRow($this);
        }

        return $this;
    }
}
