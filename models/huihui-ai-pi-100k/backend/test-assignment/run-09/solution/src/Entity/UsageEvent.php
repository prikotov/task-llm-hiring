<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования, доставленное провайдером.
 * Естественный ключ (provider_code, provider_request_id) — основа дедупликации.
 *
 * Стоимость фиксируется при тарификации (cost_usd) — итоговый отчёт
 * суммирует именно эти значения, поэтому повторный импорт/повторная
 * тарификация не меняют уже посчитанное.
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uq_usage_events', columns: ['provider_code', 'provider_request_id']),
], indexes: [
    new ORM\Index(name: 'ix_usage_events_status', columns: ['status']),
    new ORM\Index(name: 'ix_usage_events_ts', columns: ['ts']),
])]
class UsageEvent
{
    public const STATUS_PENDING = 'pending';
    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(type: 'integer')]
    private int $id = 0;

    #[ORM\Column(name: 'provider_code', type: 'string')]
    private string $providerCode = '';

    #[ORM\Column(name: 'provider_request_id', type: 'string')]
    private string $providerRequestId = '';

    #[ORM\Column(name: 'model_code', type: 'string')]
    private string $modelCode = '';

    #[ORM\Column(name: 'user_id', type: 'string')]
    private string $user = '';

    #[ORM\Column(type: 'datetime_immutable')]
    private DateTimeImmutable $ts;

    #[ORM\Column(name: 'tokens_input', type: 'bigint')]
    private int $tokensInput = 0;

    #[ORM\Column(name: 'tokens_output', type: 'bigint')]
    private int $tokensOutput = 0;

    #[ORM\Column(name: 'tokens_cache_read', type: 'bigint')]
    private int $tokensCacheRead = 0;

    #[ORM\Column(name: 'tokens_cache_write', type: 'bigint')]
    private int $tokensCacheWrite = 0;

    #[ORM\Column(type: 'string', length: 16)]
    private string $status = self::STATUS_PENDING;

    #[ORM\Column(name: 'price_row_id', type: 'integer', nullable: true)]
    private ?int $priceRowId = null;

    #[ORM\Column(name: 'cost_usd', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    #[ORM\Column(name: 'rated_at', type: 'datetime_immutable', nullable: true)]
    private ?DateTimeImmutable $ratedAt = null;

    #[ORM\Column(name: 'created_at', type: 'datetime_immutable')]
    private DateTimeImmutable $createdAt;

    public function __construct()
    {
        $this->ts = new DateTimeImmutable('2000-01-01T00:00:00+00:00');
        $this->createdAt = new DateTimeImmutable();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function setProviderCode(string $v): void
    {
        $this->providerCode = $v;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function setProviderRequestId(string $v): void
    {
        $this->providerRequestId = $v;
    }

    public function getModelCode(): string
    {
        return $this->modelCode;
    }

    public function setModelCode(string $v): void
    {
        $this->modelCode = $v;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $v): void
    {
        $this->user = $v;
    }

    public function getTs(): DateTimeImmutable
    {
        return $this->ts;
    }

    public function setTs(DateTimeImmutable $v): void
    {
        $this->ts = $v;
    }

    public function setTokens(
        int $input,
        int $output,
        int $cacheRead,
        int $cacheWrite,
    ): void {
        $this->tokensInput = $input;
        $this->tokensOutput = $output;
        $this->tokensCacheRead = $cacheRead;
        $this->tokensCacheWrite = $cacheWrite;
    }

    public function tokens(): array
    {
        return [
            'input' => $this->tokensInput,
            'output' => $this->tokensOutput,
            'cache_read' => $this->tokensCacheRead,
            'cache_write' => $this->tokensCacheWrite,
        ];
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

    public function isRated(): bool
    {
        return $this->status === self::STATUS_RATED;
    }

    public function getPriceRowId(): ?int
    {
        return $this->priceRowId;
    }

    public function getCostUsd(): ?string
    {
        return $this->costUsd;
    }

    public function markRated(int $priceRowId, string $costUsd, DateTimeImmutable $at): void
    {
        $this->status = self::STATUS_RATED;
        $this->priceRowId = $priceRowId;
        $this->costUsd = $costUsd;
        $this->ratedAt = $at;
    }

    /** Пересчёт стоимости (reprice): статус и строка цен остаются прежними. */
    public function setCostUsd(string $costUsd): void
    {
        $this->costUsd = $costUsd;
    }
}
