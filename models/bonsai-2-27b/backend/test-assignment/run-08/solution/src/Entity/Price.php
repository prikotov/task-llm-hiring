<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Строка истории цен: цена модели провайдера, действующая с valid_from
 * (включительно) до вступления следующей строки той же модели.
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="price",
 *     uniqueConstraints=[
 *         new ORM\UniqueConstraint(['provider_code', 'model_code', 'valid_from'], 'uq_price_key'),
 *     ],
 *     indexes=[
 *         new ORM\Index(['model_code', 'valid_from'], 'idx_price_lookup'),
 *     ]
 * )
 */
final class Price
{
    /** @ORM\Id @ORM\Column(type="integer", autoIncrement=true) */
    private ?int $id = null;

    /**
     * Дублируемый код провайдера — индекс для быстрого поиска.
     */
    /** @ORM\Column(name="provider_code", type="string", length=64, index=true) */
    private string $providerCode;

    /**
     * Дублируемый код модели — индекс для быстрого поиска.
     */
    /** @ORM\Column(name="model_code", type="string", length=64, index=true) */
    private string $modelCode;

    /**
     * ISO-8601 UTC, строка.
     */
    /** @ORM\Column(name="valid_from", type="string", length=32) */
    private string $validFrom;

    /** @ORM\Column(name="currency", type="string", length=16) */
    private string $currency;

    /** per_1m_tokens | per_1m_tokens_unified */
    /** @ORM\Column(name="unit", type="string", length=32) */
    private string $unit;

    /** @ORM\Column(name="rate_input", type="string", length=32, nullable=true) */
    private ?string $rateInput = null;

    /** @ORM\Column(name="rate_output", type="string", length=32, nullable=true) */
    private ?string $rateOutput = null;

    /** @ORM\Column(name="rate_cache_read", type="string", length=32, nullable=true) */
    private ?string $rateCacheRead = null;

    /** @ORM\Column(name="rate_cache_write", type="string", length=32, nullable=true) */
    private ?string $rateCacheWrite = null;

    /** @ORM\Column(name="rate_any", type="string", length=32, nullable=true) */
    private ?string $rateAny = null;

    /**
     * Ссылка на модель (для инварианта: provider/model должны существовать).
     * @ORM\ManyToOne(targetEntity: Model::class, cascade: ["remove"], onDelete: "CASCADE")
     * @ORM\JoinColumn(name="model_id", type="integer")
     */
    private ?Model $model = null;

    public function setModel(Model $model): self
    {
        $this->model = $model;
        $this->providerCode = $model->getProviderCode();
        $this->modelCode = $model->getCode();

        return $this;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setProviderCode(string $code): self
    {
        $this->providerCode = $code;

        return $this;
    }

    public function setModelCode(string $code): self
    {
        $this->modelCode = $code;

        return $this;
    }

    public function setValidFrom(string $validFrom): self
    {
        $this->validFrom = $validFrom;

        return $this;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function setUnit(string $unit): self
    {
        $this->unit = $unit;

        return $this;
    }

    public function setRates(array $rates): self
    {
        $this->rateInput = $rates['input'] ?? null;
        $this->rateOutput = $rates['output'] ?? null;
        $this->rateCacheRead = $rates['cache_read'] ?? null;
        $this->rateCacheWrite = $rates['cache_write'] ?? null;
        $this->rateAny = $rates['any'] ?? null;

        return $this;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function getModelCode(): string
    {
        return $this->modelCode;
    }

    public function getValidFrom(): string
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

    public function getRateInput(): ?string
    {
        return $this->rateInput;
    }

    public function getRateOutput(): ?string
    {
        return $this->rateOutput;
    }

    public function getRateCacheRead(): ?string
    {
        return $this->rateCacheRead;
    }

    public function getRateCacheWrite(): ?string
    {
        return $this->rateCacheWrite;
    }

    public function getRateAny(): ?string
    {
        return $this->rateAny;
    }
}
