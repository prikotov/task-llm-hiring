<?php

declare(strict_types=1);

namespace App\Domain\Usage\Entity;

use App\Domain\Pricing\Entity\Price;
use App\Domain\Usage\ValueObject\RateStatus;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(
    name: 'uniq_usage_events_provider_request',
    columns: ['provider_code', 'provider_request_id'],
)]
#[ORM\Index(name: 'idx_usage_events_occurred_at', columns: ['occurred_at'])]
#[ORM\Index(name: 'idx_usage_events_status', columns: ['status'])]
#[ORM\Index(name: 'idx_usage_events_report', columns: ['user_id', 'provider_code', 'model_code'])]
final class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'provider_code', type: 'string', length: 64)]
    private string $providerCode;

    #[ORM\Column(name: 'provider_request_id', type: 'string', length: 255)]
    private string $providerRequestId;

    #[ORM\Column(name: 'model_code', type: 'string', length: 128)]
    private string $modelCode;

    #[ORM\Column(name: 'user_id', type: 'string', length: 255)]
    private string $userId;

    #[ORM\Column(name: 'occurred_at', type: 'datetimetz_immutable')]
    private DateTimeImmutable $occurredAt;

    #[ORM\Column(name: 'tokens_input', type: 'bigint')]
    private int $tokensInput;

    #[ORM\Column(name: 'tokens_output', type: 'bigint')]
    private int $tokensOutput;

    #[ORM\Column(name: 'tokens_cache_read', type: 'bigint')]
    private int $tokensCacheRead;

    #[ORM\Column(name: 'tokens_cache_write', type: 'bigint')]
    private int $tokensCacheWrite;

    #[ORM\Column(type: 'string', length: 16, enumType: RateStatus::class)]
    private RateStatus $status = RateStatus::Unrated;

    #[ORM\Column(name: 'cost_usd', type: 'decimal', precision: 24, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    #[ORM\JoinColumn(name: 'price_id', nullable: true, onDelete: 'SET NULL')]
    private ?Price $price = null;

    #[ORM\Column(name: 'rated_at', type: 'datetimetz_immutable', nullable: true)]
    private ?DateTimeImmutable $ratedAt = null;

    public function __construct(
        string $providerCode,
        string $providerRequestId,
        string $modelCode,
        string $userId,
        DateTimeImmutable $occurredAt,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ) {
        $this->providerCode = $providerCode;
        $this->providerRequestId = $providerRequestId;
        $this->modelCode = $modelCode;
        $this->userId = $userId;
        $this->occurredAt = $occurredAt->setTimezone(new \DateTimeZone('UTC'));
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function providerCode(): string
    {
        return $this->providerCode;
    }

    public function providerRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function modelCode(): string
    {
        return $this->modelCode;
    }

    public function modelLabel(): string
    {
        return $this->providerCode.'/'.$this->modelCode;
    }

    public function userId(): string
    {
        return $this->userId;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function tokensInput(): int
    {
        return $this->tokensInput;
    }

    public function tokensOutput(): int
    {
        return $this->tokensOutput;
    }

    public function tokensCacheRead(): int
    {
        return $this->tokensCacheRead;
    }

    public function tokensCacheWrite(): int
    {
        return $this->tokensCacheWrite;
    }

    public function status(): RateStatus
    {
        return $this->status;
    }

    public function costUsd(): ?string
    {
        return $this->costUsd;
    }

    public function price(): ?Price
    {
        return $this->price;
    }

    public function ratedAt(): ?DateTimeImmutable
    {
        return $this->ratedAt;
    }

    public function markRated(Price $price, string $costUsd, DateTimeImmutable $ratedAt): void
    {
        $this->price = $price;
        $this->costUsd = $costUsd;
        $this->status = RateStatus::Rated;
        $this->ratedAt = $ratedAt;
    }

    public function markUnrated(): void
    {
        $this->price = null;
        $this->costUsd = null;
        $this->status = RateStatus::Unrated;
        $this->ratedAt = null;
    }
}
