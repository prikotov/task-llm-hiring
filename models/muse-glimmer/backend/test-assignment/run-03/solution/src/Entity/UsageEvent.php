<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_provider_request', columns: ['provider_code', 'provider_request_id'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $providerCode;

    #[ORM\Column(length: 128)]
    private string $providerRequestId;

    #[ORM\Column(length: 64)]
    private string $modelCode;

    #[ORM\Column(name: 'user_name', length: 128)]
    private string $user;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: 'bigint')]
    private int $tokensInput;

    #[ORM\Column(type: 'bigint')]
    private int $tokensOutput;

    #[ORM\Column(type: 'bigint')]
    private int $tokensCacheRead;

    #[ORM\Column(type: 'bigint')]
    private int $tokensCacheWrite;

    #[ORM\Column(type: 'decimal', precision: 20, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
    }

    public function setProviderCode(string $providerCode): void
    {
        $this->providerCode = $providerCode;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function setProviderRequestId(string $providerRequestId): void
    {
        $this->providerRequestId = $providerRequestId;
    }

    public function getModelCode(): string
    {
        return $this->modelCode;
    }

    public function setModelCode(string $modelCode): void
    {
        $this->modelCode = $modelCode;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function setUser(string $user): void
    {
        $this->user = $user;
    }

    public function getTs(): \DateTimeImmutable
    {
        return $this->ts;
    }

    public function setTs(\DateTimeImmutable $ts): void
    {
        $this->ts = $ts;
    }

    public function getTokensInput(): int
    {
        return $this->tokensInput;
    }

    public function setTokensInput(int $tokensInput): void
    {
        $this->tokensInput = $tokensInput;
    }

    public function getTokensOutput(): int
    {
        return $this->tokensOutput;
    }

    public function setTokensOutput(int $tokensOutput): void
    {
        $this->tokensOutput = $tokensOutput;
    }

    public function getTokensCacheRead(): int
    {
        return $this->tokensCacheRead;
    }

    public function setTokensCacheRead(int $tokensCacheRead): void
    {
        $this->tokensCacheRead = $tokensCacheRead;
    }

    public function getTokensCacheWrite(): int
    {
        return $this->tokensCacheWrite;
    }

    public function setTokensCacheWrite(int $tokensCacheWrite): void
    {
        $this->tokensCacheWrite = $tokensCacheWrite;
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
