<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UsageEventRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsageEventRepository::class)]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_usage_events_provider_request', columns: ['provider', 'provider_request_id'])]
final class UsageEvent
{
    public const STATUS_UNRATED = 'unrated';
    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(type: 'string', length: 64)]
    private string $provider;

    #[ORM\Column(type: 'string', length: 128)]
    private string $providerRequestId;

    #[ORM\Column(type: 'string', length: 64)]
    private string $model;

    #[ORM\Column(type: 'string', length: 128, name: 'user_code')]
    private string $user;

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

    #[ORM\Column(type: 'string', length: 16)]
    private string $status = self::STATUS_UNRATED;

    /**
     * Итоговая стоимость события, округлённая до 6 знаков (HALF_UP).
     * null, пока событие unrated.
     */
    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    private ?string $cost = null;

    #[ORM\Column(type: 'string', length: 8, nullable: true)]
    private ?string $costCurrency = null;

    /**
     * `valid_from` строки цен, которой тарифицировано событие.
     * Используется при ретро-коррекции цены (reprice).
     */
    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $appliedValidFrom = null;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $updatedAt;

    public function __construct()
    {
        $this->createdAt = new DateTimeImmutable();
        $this->updatedAt = $this->createdAt;
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

    public function setUser(string $user): self
    {
        $this->user = $user;

        return $this;
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

    public function getCost(): ?string
    {
        return $this->cost;
    }

    public function getCostCurrency(): ?string
    {
        return $this->costCurrency;
    }

    public function getAppliedValidFrom(): ?DateTimeImmutable
    {
        return $this->appliedValidFrom;
    }

    public function getCreatedAt(): DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): DateTimeImmutable
    {
        return $this->updatedAt;
    }

    public function isRated(): bool
    {
        return $this->status === self::STATUS_RATED;
    }

    // --- заполнение при импорте ---

    public function setImport(
        string $provider,
        string $providerRequestId,
        string $model,
        string $user,
        DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ): self {
        $this->provider = $provider;
        $this->providerRequestId = $providerRequestId;
        $this->model = $model;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
        $this->status = self::STATUS_UNRATED;
        $this->cost = null;
        $this->costCurrency = null;
        $this->appliedValidFrom = null;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    // --- тарификация / переоценка ---

    public function markRated(string $cost, string $currency, DateTimeImmutable $appliedValidFrom): self
    {
        $this->status = self::STATUS_RATED;
        $this->cost = $cost;
        $this->costCurrency = $currency;
        $this->appliedValidFrom = $appliedValidFrom;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }

    public function markUnrated(): self
    {
        $this->status = self::STATUS_UNRATED;
        $this->cost = null;
        $this->costCurrency = null;
        $this->appliedValidFrom = null;
        $this->updatedAt = new DateTimeImmutable();

        return $this;
    }
}
