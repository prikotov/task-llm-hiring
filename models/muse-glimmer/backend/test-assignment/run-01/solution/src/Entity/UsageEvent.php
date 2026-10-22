<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_provider_request', columns: ['provider_code', 'provider_request_id'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private int $id;

    #[ORM\Column(name: 'provider_code', type: 'string', length: 50)]
    private string $providerCode;

    #[ORM\Column(name: 'provider_request_id', type: 'string', length: 100)]
    private string $providerRequestId;

    #[ORM\Column(name: 'model_code', type: 'string', length: 50)]
    private string $modelCode;

    #[ORM\Column(name: 'user_id', type: 'string', length: 100)]
    private string $user;

    #[ORM\Column(type: 'datetimetz_immutable')]
    private \DateTimeImmutable $ts;

    #[ORM\Column(name: 'tokens_input', type: 'bigint')]
    private int $tokensInput;

    #[ORM\Column(name: 'tokens_output', type: 'bigint')]
    private int $tokensOutput;

    #[ORM\Column(name: 'tokens_cache_read', type: 'bigint')]
    private int $tokensCacheRead;

    #[ORM\Column(name: 'tokens_cache_write', type: 'bigint')]
    private int $tokensCacheWrite;

    #[ORM\Column(name: 'cost_usd', type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    public function __construct(
        string $providerCode,
        string $providerRequestId,
        string $modelCode,
        string $user,
        \DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite
    ) {
        $this->providerCode = $providerCode;
        $this->providerRequestId = $providerRequestId;
        $this->modelCode = $modelCode;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function getModelCode(): string
    {
        return $this->modelCode;
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

    public function getCostUsd(): ?string
    {
        return $this->costUsd;
    }

    public function setCostUsd(?string $costUsd): void
    {
        $this->costUsd = $costUsd;
    }

    public function isRated(): bool
    {
        return $this->costUsd !== null;
    }
}
