<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (строка журнала).
 *
 * @ORM\Entity
 * @ORM\Table(name: "usage_events")
 */
final class UsageEvent
{
    const STATUS_NEW = 'new';
    const STATUS_RATED = 'rated';
    const STATUS_UNRATED = 'unrated';

    /**
     * @ORM\Id
     * @ORM\Column(type: "integer")
     */
    private int $id;

    /**
     * @ORM\Column(type: "string", length: 255)
     */
    private string $provider;

    /**
     * @ORM\Column(type: "string", length: 255)
     */
    private string $providerRequestId;

    /**
     * @ORM\Column(type: "string", length: 255)
     */
    private string $model;

    /**
     * @ORM\Column(type: "string", length: 255)
     */
    private string $user;

    /**
     * @ORM\Column(type: "datetime")
     */
    private \DateTimeImmutable $ts;

    /**
     * @ORM\Column(type: "integer")
     */
    private int $tokensInput = 0;

    /**
     * @ORM\Column(type: "integer")
     */
    private int $tokensOutput = 0;

    /**
     * @ORM\Column(type: "integer")
     */
    private int $tokensCacheRead = 0;

    /**
     * @ORM\Column(type: "integer")
     */
    private int $tokensCacheWrite = 0;

    /**
     * Статус: new / rated / unrated
     * @ORM\Column(type: "string", length: 16, defaultValue: 'new')
     */
    private string $status = self::STATUS_NEW;

    /**
     * Используемая строка цен (при rated).
     * @ORM\ManyToOne
     * @ORM\JoinColumn(name: "price_row_id", target_entity: "App\Entity\PriceRow",
     *     referenced_column_name: "id")
     */
    private ?PriceRow $priceRow = null;

    /**
     * Итоговая стоимость события (после округления до 6 знаков).
     * @ORM\Column(type: "string", length: 64) nullable=true
     */
    private ?string $cost = null;

    public function __construct()
    {
        $this->id = 0;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function setProviderRequestId(string $providerRequestId): void
    {
        $this->providerRequestId = $providerRequestId;
    }

    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    public function setTs(\DateTimeImmutable $ts): void
    {
        $this->ts = $ts;
    }

    public function setTokens(int $input, int $output, int $cacheRead, int $cacheWrite): void
    {
        $this->tokensInput = $input;
        $this->tokensOutput = $output;
        $this->tokensCacheRead = $cacheRead;
        $this->tokensCacheWrite = $cacheWrite;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setPriceRow(?PriceRow $priceRow): void
    {
        $this->priceRow = $priceRow;
    }

    public function getPriceRow(): ?PriceRow
    {
        return $this->priceRow;
    }

    public function setCost(?string $cost): void
    {
        $this->cost = $cost;
    }

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getTs(): \DateTimeImmutable
    {
        return $this->ts;
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

    /**
     * Естественный ключ события.
     */
    public function eventKey(): string
    {
        return $this->provider . '|' . $this->providerRequestId;
    }

    public function __toString(): string
    {
        return $this->provider . '/' . $this->providerRequestId;
    }
}
