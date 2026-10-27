<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\Column(length: 64)]
    private string $providerCode;

    #[ORM\Id]
    #[ORM\Column(length: 128)]
    private string $providerRequestId;

    #[ORM\Column(length: 64)]
    private string $modelCode;

    #[ORM\Column(name: 'user_name', length: 128)]
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
    private ?string $costUsd = null;

    public function getProviderCode(): string { return $this->providerCode; }
    public function setProviderCode(string $c): void { $this->providerCode = $c; }
    public function getProviderRequestId(): string { return $this->providerRequestId; }
    public function setProviderRequestId(string $id): void { $this->providerRequestId = $id; }
    public function getModelCode(): string { return $this->modelCode; }
    public function setModelCode(string $c): void { $this->modelCode = $c; }
    public function getUser(): string { return $this->user; }
    public function setUser(string $u): void { $this->user = $u; }
    public function getTs(): \DateTimeImmutable { return $this->ts; }
    public function setTs(\DateTimeImmutable $dt): void { $this->ts = $dt; }
    public function getTokensInput(): int { return $this->tokensInput; }
    public function setTokensInput(int $v): void { $this->tokensInput = $v; }
    public function getTokensOutput(): int { return $this->tokensOutput; }
    public function setTokensOutput(int $v): void { $this->tokensOutput = $v; }
    public function getTokensCacheRead(): int { return $this->tokensCacheRead; }
    public function setTokensCacheRead(int $v): void { $this->tokensCacheRead = $v; }
    public function getTokensCacheWrite(): int { return $this->tokensCacheWrite; }
    public function setTokensCacheWrite(int $v): void { $this->tokensCacheWrite = $v; }
    public function getCostUsd(): ?string { return $this->costUsd; }
    public function setCostUsd(?string $c): void { $this->costUsd = $c; }
}
