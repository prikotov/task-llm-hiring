<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_usage_provider_request', columns: ['provider_code', 'provider_request_id'])]
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

    #[ORM\Column(name: 'user_id', length: 128)]
    private string $user;

    #[ORM\Column(type: 'datetime_immutable')]
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
    private ?string $cost = null;

    public function getId(): ?int { return $this->id; }

    public function getProviderCode(): string { return $this->providerCode; }
    public function setProviderCode(string $v): void { $this->providerCode = $v; }

    public function getProviderRequestId(): string { return $this->providerRequestId; }
    public function setProviderRequestId(string $v): void { $this->providerRequestId = $v; }

    public function getModelCode(): string { return $this->modelCode; }
    public function setModelCode(string $v): void { $this->modelCode = $v; }

    public function getUser(): string { return $this->user; }
    public function setUser(string $v): void { $this->user = $v; }

    public function getTs(): \DateTimeImmutable { return $this->ts; }
    public function setTs(\DateTimeImmutable $v): void { $this->ts = $v; }

    public function getTokensInput(): int { return $this->tokensInput; }
    public function setTokensInput(int $v): void { $this->tokensInput = $v; }

    public function getTokensOutput(): int { return $this->tokensOutput; }
    public function setTokensOutput(int $v): void { $this->tokensOutput = $v; }

    public function getTokensCacheRead(): int { return $this->tokensCacheRead; }
    public function setTokensCacheRead(int $v): void { $this->tokensCacheRead = $v; }

    public function getTokensCacheWrite(): int { return $this->tokensCacheWrite; }
    public function setTokensCacheWrite(int $v): void { $this->tokensCacheWrite = $v; }

    public function getCost(): ?string { return $this->cost; }
    public function setCost(?string $v): void { $this->cost = $v; }

    public function isRated(): bool { return $this->cost !== null; }
}
