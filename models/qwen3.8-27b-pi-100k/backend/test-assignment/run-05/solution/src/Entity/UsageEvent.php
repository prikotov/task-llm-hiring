<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_event')]
#[ORM\UniqueConstraint(name: 'uniq_usage_event_provider_request', columns: ['provider', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_event_status', columns: ['status'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'bigint')]
    private int $id = 0;

    #[ORM\Column(type: 'string', length: 255)]
    private string $provider = '';

    #[ORM\Column(type: 'string', length: 255)]
    private string $providerRequestId = '';

    #[ORM\Column(type: 'string', length: 255)]
    private string $model = '';

    #[ORM\Column(name: 'user_name', type: 'string', length: 255)]
    private string $user = '';

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $ts;

    #[ORM\Column(type: 'bigint')]
    private int $tokensInput = 0;

    #[ORM\Column(type: 'bigint')]
    private int $tokensOutput = 0;

    #[ORM\Column(type: 'bigint')]
    private int $tokensCacheRead = 0;

    #[ORM\Column(type: 'bigint')]
    private int $tokensCacheWrite = 0;

    /** UsageEventStatus::*. */
    #[ORM\Column(type: 'string', length: 16)]
    private string $status = UsageEventStatus::NEW;

    /** Округлённая (HALF_UP, 6 знаков) итоговая стоимость события; null, пока unrated. */
    #[ORM\Column(type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    #[ORM\JoinColumn(name: 'price_row_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?PriceRow $priceRow = null;

    public function __construct(
        string $provider,
        string $providerRequestId,
        string $model,
        string $user,
        DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ) {
        $this->provider = $provider;
        $this->providerRequestId = $providerRequestId;
        $this->model = $model;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
    }

    public function getId(): int
    {
        return $this->id;
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

    public function getTs(): DateTimeImmutable
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCostUsd(): ?string
    {
        return $this->costUsd;
    }

    public function getPriceRow(): ?PriceRow
    {
        return $this->priceRow;
    }

    public function markRated(string $costUsd, PriceRow $priceRow): void
    {
        $this->status = UsageEventStatus::RATED;
        $this->costUsd = $costUsd;
        $this->priceRow = $priceRow;
    }

    public function markUnrated(): void
    {
        $this->status = UsageEventStatus::UNRATED;
        $this->costUsd = null;
        $this->priceRow = null;
    }
}
