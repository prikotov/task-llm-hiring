<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(columns: ['provider', 'provider_request_id'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 50)]
    private string $provider;

    #[ORM\Column(type: 'string', length: 255)]
    private string $providerRequestId;

    #[ORM\Column(type: 'string', length: 100)]
    private string $model;

    #[ORM\Column(name: 'user_id', type: 'string', length: 255)]
    private string $user;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: 'integer')]
    private int $tokensInput;

    #[ORM\Column(type: 'integer')]
    private int $tokensOutput;

    #[ORM\Column(type: 'integer')]
    private int $tokensCacheRead;

    #[ORM\Column(type: 'integer')]
    private int $tokensCacheWrite;

    #[ORM\ManyToOne(targetEntity: Price::class)]
    #[ORM\JoinColumn(nullable: true)]
    private ?Price $price = null;

    #[ORM\Column(type: 'string', precision: 18, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    public function getPrice(): ?Price
    {
        return $this->price;
    }

    public function setPrice(?Price $price): void
    {
        $this->price = $price;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProvider(): string
    {
        return $this->provider;
    }

    public function setProvider(string $provider): void
    {
        $this->provider = $provider;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function setProviderRequestId(string $providerRequestId): void
    {
        $this->providerRequestId = $providerRequestId;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): void
    {
        $this->model = $model;
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
}
