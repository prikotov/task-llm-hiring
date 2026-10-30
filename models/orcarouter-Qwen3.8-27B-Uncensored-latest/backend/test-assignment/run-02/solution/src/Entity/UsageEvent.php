<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events',
    uniqueConstraints: [
        new ORM\UniqueConstraint(name: 'uniq_usage_events_provider_request', columns: ['provider', 'provider_request_id']),
    ],
    indexes: [
        new ORM\Index(name: 'idx_usage_events_report', columns: ['user_id', 'provider', 'model_code']),
        new ORM\Index(name: 'idx_usage_events_status', columns: ['status']),
        new ORM\Index(name: 'idx_usage_events_ts', columns: ['ts']),
    ],
)]
final class UsageEvent
{
    /** Событие принято, ещё не рассчитано (или не нашлась цена). */
    public const STATUS_PENDING = 'pending';

    /** Событие успешно тарифицировано. */
    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\Column(type: 'bigint')]
    #[ORM\GeneratedValue]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $provider;

    #[ORM\Column(name: 'provider_request_id', type: 'string', length: 255)]
    private string $providerRequestId;

    /** Код модели со стороны каталога (без префикса провайдера). */
    #[ORM\Column(name: 'model_code', type: 'string', length: 128)]
    private string $modelCode;

    #[ORM\Column(name: 'user_id', type: 'string', length: 255)]
    private string $userId;

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $ts;

    #[ORM\Column(name: 'tokens_input', type: 'bigint')]
    private int $tokensInput;

    #[ORM\Column(name: 'tokens_output', type: 'bigint')]
    private int $tokensOutput;

    #[ORM\Column(name: 'tokens_cache_read', type: 'bigint')]
    private int $tokensCacheRead;

    #[ORM\Column(name: 'tokens_cache_write', type: 'bigint')]
    private int $tokensCacheWrite;

    #[ORM\Column(type: 'string', length: 16)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(name: 'price_row_id', type: 'integer', nullable: true)]
    private ?int $priceRowId = null;

    /** Итоговая стоимость события в USD — десятичная строка, 6 знаков. */
    #[ORM\Column(name: 'cost_usd', type: 'string', nullable: true)]
    private ?string $costUsd = null;

    #[ORM\Column(name: 'rated_at', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $ratedAt = null;

    public function __construct(
        string $provider,
        string $providerRequestId,
        string $modelCode,
        string $userId,
        DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ) {
        $this->provider = $provider;
        $this->providerRequestId = $providerRequestId;
        $this->modelCode = $modelCode;
        $this->userId = $userId;
        $this->ts = $ts;
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
    }

    public function getId(): ?int
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

    public function getModelCode(): string
    {
        return $this->modelCode;
    }

    public function getUserId(): string
    {
        return $this->userId;
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

    public function getPriceRowId(): ?int
    {
        return $this->priceRowId;
    }

    public function getCostUsd(): ?string
    {
        return $this->costUsd;
    }

    public function getRatedAt(): ?DateTimeImmutable
    {
        return $this->ratedAt;
    }

    public function markRated(int $priceRowId, string $costUsd, DateTimeImmutable $at): void
    {
        $this->status = self::STATUS_RATED;
        $this->priceRowId = $priceRowId;
        $this->costUsd = $costUsd;
        $this->ratedAt = $at;
    }
}
