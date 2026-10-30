<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_provider_request', columns: ['provider_code', 'provider_request_id'])
])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(length: 64)]
    private string $providerCode;

    #[ORM\Column(length: 128)]
    private string $providerRequestId;

    #[ORM\Column(length: 64)]
    private string $modelCode;

    #[ORM\Column(name: 'user_name', length: 255)]
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

    public function getId(): ?int { return $this->id; }
    public function getProviderCode(): string { return $this->providerCode; }
    public function getProviderRequestId(): string { return $this->providerRequestId; }
    public function getModelCode(): string { return $this->modelCode; }
    public function getUser(): string { return $this->user; }
    public function getTs(): \DateTimeImmutable { return $this->ts; }
    public function getTokensInput(): int { return $this->tokensInput; }
    public function getTokensOutput(): int { return $this->tokensOutput; }
    public function getTokensCacheRead(): int { return $this->tokensCacheRead; }
    public function getTokensCacheWrite(): int { return $this->tokensCacheWrite; }
    public function getCostUsd(): ?string { return $this->costUsd; }
    public function setCostUsd(?string $cost): void { $this->costUsd = $cost; }
    public function isRated(): bool { return $this->costUsd !== null; }
}
