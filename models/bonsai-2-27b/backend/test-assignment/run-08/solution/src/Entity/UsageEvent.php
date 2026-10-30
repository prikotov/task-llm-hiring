<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\VarExporter\Attribute\VarExporter\Exported;

/**
 * Событие использования: один запрос к модели.
 *
 * @ORM\Entity
 * @ORM\Table(
 *     name="usage_event",
 *     uniqueConstraints=[
 *         new ORM\UniqueConstraint(['provider_code', 'provider_request_id'], 'uq_event_key'),
 *     ],
 *     indexes=[
 *         new ORM\Index(['provider_code', 'provider_request_id'], 'idx_event_key'),
 *     ]
 * )
 */
final class UsageEvent
{
    /** @ORM\Id @ORM\Column(type="integer", autoIncrement=true) */
    private ?int $id = null;

    /**
     * Связь с провайдером: provider_code дублируется для поиска.
     */
    /** @ORM\Column(name="provider_code", type="string", length=64, index=true) */
    private string $providerCode;

    /**
     * Идентификатор запроса у провайдера.
     */
    /** @ORM\Column(name="provider_request_id", type="string", length=128) */
    private string $providerRequestId;

    /**
     * Дублируемый код модели: provider_code/model_code.
     */
    /** @ORM\Column(name="model_code", type="string", length=128, index=true) */
    private string $modelCode;

    /**
     * @ORM\ManyToOne(targetEntity: Provider::class, cascade: ["remove"], onDelete: "CASCADE", fetch="EAGER")
     */
    private ?Provider $provider = null;

    /**
     * @ORM\ManyToOne(targetEntity: Model::class, cascade: ["remove"], onDelete: "CASCADE")
     */
    private ?Model $model = null;

    /** Идентификатор пользователя. */
    /** @ORM\Column(name="user", type="string", length=128, index=true) */
    private string $user;

    /** ISO-8601 UTC. */
    /** @ORM\Column(name="ts", type="string", length=32, index=true) */
    private string $ts;

    /** @ORM\Column(name="tokens_input", type="integer") */
    private int $tokensInput;

    /** @ORM\Column(name="tokens_output", type="integer") */
    private int $tokensOutput;

    /** @ORM\Column(name="tokens_cache_read", type="integer") */
    private int $tokensCacheRead;

    /** @ORM\Column(name="tokens_cache_write", type="integer") */
    private int $tokensCacheWrite;

    /**
     * Итоговая стоимость события (USD), округлённая до 6 знаков (HALF_UP).
     * null — событие ещё не рассчитано или unrated.
     */
    /** @ORM\Column(name="cost_usd", type="string", length=32, nullable=true) */
    private ?string $costUsd = null;

    /**
     * Динамика стоимости при пересчёте (reprice). null — не пересчитывалось.
     */
    /** @ORM\Column(name="reprice_delta_usd", type="string", length=32, nullable=true) */
    private ?string $repriceDeltaUsd = null;

    /**
     * Ссылка на цену, по которой тарифицировано (если рассчитано).
     */
    /** @ORM\ManyToOne(targetEntity: Price::class, cascade: ["remove"], onDelete: "SET NULL") */
    private ?Price $appliedPrice = null;

    public function setProviderCode(string $code): self
    {
        $this->providerCode = $code;

        return $this;
    }

    public function setProviderRequestId(string $id): self
    {
        $this->providerRequestId = $id;

        return $this;
    }

    public function setModelCode(string $code): self
    {
        $this->modelCode = $code;

        return $this;
    }

    /** @ORM\ManyToOne(targetEntity: Provider::class, cascade: ["remove"], onDelete: "CASCADE", fetch="EAGER") */
    public function setProvider(Provider $provider): self
    {
        $this->provider = $provider;
        $this->providerCode = $provider->getCode();

        return $this;
    }

    public function getProvider(): ?Provider
    {
        return $this->provider;
    }

    /** @ORM\ManyToOne(targetEntity: Model::class, cascade: ["remove"], onDelete: "CASCADE") */
    public function setModel(Model $model): self
    {
        $this->model = $model;
        $this->modelCode = $model->getFullCode();

        return $this;
    }

    public function getModel(): ?Model
    {
        return $this->model;
    }

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setTs(string $ts): self
    {
        $this->ts = $ts;

        return $this;
    }

    public function getTs(): string
    {
        return $this->ts;
    }

    public function setTokensInput(int $n): self
    {
        $this->tokensInput = $n;

        return $this;
    }

    public function setTokensOutput(int $n): self
    {
        $this->tokensOutput = $n;

        return $this;
    }

    public function setTokensCacheRead(int $n): self
    {
        $this->tokensCacheRead = $n;

        return $this;
    }

    public function setTokensCacheWrite(int $n): self
    {
        $this->tokensCacheWrite = $n;

        return $this;
    }

    public function getTokensInput(): int
    {
        return $this->tokensInput;
    }

    public function getTokensOutput(): int
    {
        return $this->tokensOutput;
    }

    public function getTokensCacheRead(): int
    {
        return $this->tokensCacheRead;
    }

    public function getTokensCacheWrite(): int
    {
        return $this->tokensCacheWrite;
    }

    public function setCostUsd(string $cost): self
    {
        $this->costUsd = $cost;

        return $this;
    }

    public function getCostUsd(): ?string
    {
        return $this->costUsd;
    }

    public function isRated(): bool
    {
        return $this->costUsd !== null;
    }

    public function setRepriceDeltaUsd(?string $delta): self
    {
        $this->repriceDeltaUsd = $delta;

        return $this;
    }

    public function getRepriceDeltaUsd(): ?string
    {
        return $this->repriceDeltaUsd;
    }

    public function setAppliedPrice(Price $price): self
    {
        $this->appliedPrice = $price;

        return $this;
    }

    public function getAppliedPrice(): ?Price
    {
        return $this->appliedPrice;
    }
}
