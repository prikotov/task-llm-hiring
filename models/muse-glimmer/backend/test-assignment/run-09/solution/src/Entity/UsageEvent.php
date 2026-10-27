<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_provider_request', columns: ['provider', 'provider_request_id'])
])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 64)]
    private string $provider;

    #[ORM\Column(type: 'string', length: 128)]
    private string $providerRequestId;

    #[ORM\Column(type: 'string', length: 64)]
    private string $model;

    #[ORM\Column(type: 'string', length: 128, name: 'user_name')]
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
    private ?string $cost = null;

    public function getId(): ?int { return $this->id; }
    public function getProvider(): string { return $this->provider; }
    public function setProvider(string $v): void { $this->provider = $v; }
    public function getProviderRequestId(): string { return $this->providerRequestId; }
    public function setProviderRequestId(string $v): void { $this->providerRequestId = $v; }
    public function getModel(): string { return $this->model; }
    public function setModel(string $v): void { $this->model = $v; }
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
