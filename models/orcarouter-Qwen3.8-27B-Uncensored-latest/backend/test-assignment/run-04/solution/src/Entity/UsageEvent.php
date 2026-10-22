<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Pricing\UsageRecord;
use Doctrine\ORM\Mapping as ORM;

/**
 * A usage event as reported by a provider (immutable import data + rating
 * result). Natural key: (provider, providerRequestId) — idempotent import is
 * enforced by the unique constraint.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_usage_events_provider_request', columns: ['provider', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_rating', columns: ['provider', 'model', 'status'])]
#[ORM\Index(name: 'idx_usage_events_report', columns: ['user', 'provider', 'model', 'ts'])]
class UsageEvent
{
    /** @var self::STATUS_UNRATED|self::STATUS_RATED */
    public const STATUS_UNRATED = 'unrated';
    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    private int $id;

    public function __construct(
        #[ORM\Column(type: 'string', length: 64)]
        private string $provider,

        #[ORM\Column(type: 'string', length: 128)]
        private string $providerRequestId,

        #[ORM\Column(type: 'string', length: 64)]
        private string $model,

        #[ORM\Column(name: 'user', type: 'string', length: 128)]
        private string $userId,

        #[ORM\Column(type: 'datetimetz_immutable')]
        private \DateTimeImmutable $ts,

        #[ORM\Column(type: 'bigint')]
        private int $tokensInput,

        #[ORM\Column(type: 'bigint')]
        private int $tokensOutput,

        #[ORM\Column(type: 'bigint')]
        private int $tokensCacheRead,

        #[ORM\Column(type: 'bigint')]
        private int $tokensCacheWrite,
    ) {
    }

    #[ORM\Column(type: 'string', length: 16)]
    private string $status = self::STATUS_UNRATED;

    #[ORM\Column(type: 'bigint', nullable: true)]
    private ?int $priceRowId = null;

    #[ORM\Column(type: 'decimal', precision: 30, scale: 10, nullable: true)]
    private ?string $cost = null;

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

    public function getUserId(): string
    {
        return $this->userId;
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

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPriceRowId(): ?int
    {
        return $this->priceRowId;
    }

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function isRated(): bool
    {
        return $this->status === self::STATUS_RATED;
    }

    public function markRated(int $priceRowId, string $cost): void
    {
        $this->status = self::STATUS_RATED;
        $this->priceRowId = $priceRowId;
        $this->cost = $cost;
    }

    public function toUsageRecord(): UsageRecord
    {
        return new UsageRecord(
            $this->tokensInput,
            $this->tokensOutput,
            $this->tokensCacheRead,
            $this->tokensCacheWrite,
            $this->ts,
        );
    }
}
