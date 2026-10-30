<?php

declare(strict_types=1);

namespace App\Infrastructure\Persistence\Doctrine\Entity;

use App\Domain\Money\Decimal;
use App\Domain\Usage\TokenUsage;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_usage_events_provider_request', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_occurred_at', columns: ['occurred_at'])]
#[ORM\Index(name: 'idx_usage_events_unrated', columns: ['cost_usd'])]
#[ORM\Index(name: 'idx_usage_events_report', columns: ['user_id', 'provider_code', 'model_code'])]
final class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private ?int $id = null;

    #[ORM\Column(name: 'provider_code', type: 'string', length: 64)]
    private string $providerCode;

    #[ORM\Column(name: 'provider_request_id', type: 'string', length: 255)]
    private string $providerRequestId;

    #[ORM\Column(name: 'model_code', type: 'string', length: 128)]
    private string $modelCode;

    #[ORM\Column(name: 'user_id', type: 'string', length: 255)]
    private string $userId;

    #[ORM\Column(name: 'occurred_at', type: 'datetimetz_immutable', precision: 6)]
    private DateTimeImmutable $occurredAt;

    #[ORM\Column(name: 'tokens_input', type: 'bigint')]
    private int $tokensInput;

    #[ORM\Column(name: 'tokens_output', type: 'bigint')]
    private int $tokensOutput;

    #[ORM\Column(name: 'tokens_cache_read', type: 'bigint')]
    private int $tokensCacheRead;

    #[ORM\Column(name: 'tokens_cache_write', type: 'bigint')]
    private int $tokensCacheWrite;

    #[ORM\Column(name: 'cost_usd', type: 'decimal', precision: 24, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    #[ORM\JoinColumn(name: 'price_id', referencedColumnName: 'id', nullable: true, onDelete: 'SET NULL')]
    private ?Price $price = null;

    #[ORM\Column(name: 'rated_at', type: 'datetimetz_immutable', precision: 6, nullable: true)]
    private ?DateTimeImmutable $ratedAt = null;

    #[ORM\Column(name: 'repriced_at', type: 'datetimetz_immutable', precision: 6, nullable: true)]
    private ?DateTimeImmutable $repricedAt = null;

    public function __construct(
        string $providerCode,
        string $providerRequestId,
        string $modelCode,
        string $userId,
        DateTimeImmutable $occurredAt,
        TokenUsage $usage,
    ) {
        $this->providerCode = $providerCode;
        $this->providerRequestId = $providerRequestId;
        $this->modelCode = $modelCode;
        $this->userId = $userId;
        $this->occurredAt = $occurredAt;
        $this->tokensInput = $usage->input;
        $this->tokensOutput = $usage->output;
        $this->tokensCacheRead = $usage->cacheRead;
        $this->tokensCacheWrite = $usage->cacheWrite;
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

    public function userId(): string
    {
        return $this->userId;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function usage(): TokenUsage
    {
        return new TokenUsage(
            $this->tokensInput,
            $this->tokensOutput,
            $this->tokensCacheRead,
            $this->tokensCacheWrite,
        );
    }

    public function costUsd(): ?Decimal
    {
        return $this->costUsd === null ? null : Decimal::fromString($this->costUsd);
    }

    public function price(): ?Price
    {
        return $this->price;
    }

    public function isRated(): bool
    {
        return $this->costUsd !== null;
    }

    public function markRated(Price $price, Decimal $cost, DateTimeImmutable $ratedAt): void
    {
        $this->price = $price;
        $this->costUsd = $cost->format(6);
        $this->ratedAt = $ratedAt;
    }

    public function applyReprice(Price $price, Decimal $cost, DateTimeImmutable $repricedAt): void
    {
        $this->price = $price;
        $this->costUsd = $cost->format(6);
        $this->repricedAt = $repricedAt;
    }
}
