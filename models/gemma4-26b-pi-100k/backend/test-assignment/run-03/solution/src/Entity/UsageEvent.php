<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'usage_event_unique_idx', columns: ['provider', 'provider_request_id'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $provider;

    #[ORM\Column(type: 'string')]
    private string $providerRequestId;

    #[ORM\Column(type: 'string')]
    private string $modelCode;

    #[ORM\Column(type: 'string', name: 'username')]
    private string $user;

    #[ORM\Column(type: 'datetime')]
    private \DateTime $ts;

    #[ORM\Column(type: 'integer')]
    private int $tokensInput;

    #[ORM\Column(type: 'integer')]
    private int $tokensOutput;

    #[ORM\Column(type: 'integer')]
    private int $tokensCacheRead;

    #[ORM\Column(type: 'integer')]
    private int $tokensCacheWrite;

    #[ORM\Column(type: 'decimal', precision: 16, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    #[ORM\Column(type: 'boolean')]
    private bool $isRated = false;

    public function __construct(
        string $provider,
        string $providerRequestId,
        string $modelCode,
        string $user,
        \DateTime $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite
    ) {
        $this->provider = $provider;
        $this->providerRequestId = $providerRequestId;
        $this->modelCode = $modelCode;
        $this->user = $user;
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

    public function getUser(): string
    {
        return $this->user;
    }

    public function getTs(): \DateTime
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

    public function setCostUsd(string $costUsd): void
    {
        $this->costUsd = $costUsd;
    }

    public function isRated(): bool
    {
        return $this->isRated;
    }

    public function setRated(bool $isRated): void
    {
        $this->isRated = $isRated;
    }
}
