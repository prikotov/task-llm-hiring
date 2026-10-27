<?php

declare(strict_types=1);

namespace App\Domain;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\UsageEventRepository;

/**
 * A single usage event reported by a provider.
 *
 * Dedup key: (provider_code, provider_request_id).
 *
 * Rating state machine:
 *   pending  -> rated | unrated   (by app:billing:rate)
 *   unrated  -> rated             (rate retry, price may have appeared)
 *   rated    -> rated             (cost replaced by app:billing:reprice)
 */
#[ORM\Entity(repositoryClass: UsageEventRepository::class)]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uq_usage_events_natural_key', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_status', columns: ['status'])]
#[ORM\Index(name: 'idx_usage_events_ts', columns: ['ts_utc'])]
#[ORM\Index(name: 'idx_usage_events_price_ref', columns: ['rated_provider', 'rated_model', 'rated_price_valid_from'])]
class UsageEvent
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RATED = 'rated';
    public const STATUS_UNRATED = 'unrated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $id;

    #[ORM\Column(length: 64)]
    private string $providerCode;

    #[ORM\Column(length: 128)]
    private string $providerRequestId;

    #[ORM\Column(length: 64)]
    private string $modelCode;

    #[ORM\Column(length: 128)]
    private string $userName;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $tsUtc;

    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $tokensInput = 0;

    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $tokensOutput = 0;

    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $tokensCacheRead = 0;

    #[ORM\Column(type: 'bigint', options: ['unsigned' => true])]
    private int $tokensCacheWrite = 0;

    #[ORM\Column(length: 16)]
    private string $status = self::STATUS_PENDING;

    /** Decimal string, exactly 6 places, HALF_UP at rating time. */
    #[ORM\Column(nullable: true)]
    private ?string $costUsd = null;

    /** Provider/model/validFrom of the price line used for current cost. */
    #[ORM\Column(length: 64, nullable: true)]
    private ?string $ratedProvider = null;

    #[ORM\Column(length: 64, nullable: true)]
    private ?string $ratedModel = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $ratedPriceValidFrom = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?\DateTimeImmutable $ratedAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $importedAt;

    public function __construct(
        string $providerCode,
        string $providerRequestId,
        string $modelCode,
        string $userName,
        \DateTimeImmutable $tsUtc,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        ?\DateTimeImmutable $importedAt = null,
    ) {
        $this->providerCode = $providerCode;
        $this->providerRequestId = $providerRequestId;
        $this->modelCode = $modelCode;
        $this->userName = $userName;
        $this->tsUtc = $tsUtc;
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
        $this->importedAt = $importedAt ?? new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function rate(PriceLine $line, string $costUsd): void
    {
        $this->status = self::STATUS_RATED;
        $this->costUsd = $costUsd;
        $this->ratedProvider = $line->providerCode();
        $this->ratedModel = $line->modelCode();
        $this->ratedPriceValidFrom = $line->validFrom();
        $this->ratedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    public function markUnrated(): void
    {
        $this->status = self::STATUS_UNRATED;
        $this->costUsd = null;
        $this->ratedProvider = null;
        $this->ratedModel = null;
        $this->ratedPriceValidFrom = null;
        $this->ratedAt = new \DateTimeImmutable('now', new \DateTimeZone('UTC'));
    }

    // --- accessors ---

    public function id(): int
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

    public function userName(): string
    {
        return $this->userName;
    }

    public function tsUtc(): \DateTimeImmutable
    {
        return $this->tsUtc;
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

    public function status(): string
    {
        return $this->status;
    }

    public function costUsd(): ?string
    {
        return $this->costUsd;
    }

    public function ratedProvider(): ?string
    {
        return $this->ratedProvider;
    }

    public function ratedModel(): ?string
    {
        return $this->ratedModel;
    }

    public function ratedPriceValidFrom(): ?\DateTimeImmutable
    {
        return $this->ratedPriceValidFrom;
    }
}
