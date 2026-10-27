<?php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(columns: ['provider_code','provider_request_id'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type:'integer')]
    private ?int $id=null;

    #[ORM\Column(length:50)]
    private string $providerCode;

    #[ORM\Column(length:100)]
    private string $providerRequestId;

    #[ORM\Column(length:100)]
    private string $modelCode;

    #[ORM\Column(name:'user_id', length:100)]
    private string $user;

    #[ORM\Column(type:'datetimetz_immutable')]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type:'bigint')]
    private int $tokensInput;

    #[ORM\Column(type:'bigint')]
    private int $tokensOutput;

    #[ORM\Column(type:'bigint')]
    private int $tokensCacheRead;

    #[ORM\Column(type:'bigint')]
    private int $tokensCacheWrite;

    #[ORM\Column(type:'decimal', precision:20, scale:6, nullable:true)]
    private ?string $costUsd = null;

    #[ORM\Column(type:'boolean')]
    private bool $rated = false;

    public function __construct(string $providerCode, string $providerRequestId){
        $this->providerCode=$providerCode;
        $this->providerRequestId=$providerRequestId;
    }

    public function getProviderCode(): string { return $this->providerCode; }
    public function getProviderRequestId(): string { return $this->providerRequestId; }
    public function getModelCode(): string { return $this->modelCode; }
    public function setModelCode(string $v): void { $this->modelCode=$v; }
    public function getUser(): string { return $this->user; }
    public function setUser(string $v): void { $this->user=$v; }
    public function getTs(): \DateTimeImmutable { return $this->ts; }
    public function setTs(\DateTimeImmutable $v): void { $this->ts=$v; }
    public function getTokensInput(): int { return $this->tokensInput; }
    public function setTokensInput(int $v): void { $this->tokensInput=$v; }
    public function getTokensOutput(): int { return $this->tokensOutput; }
    public function setTokensOutput(int $v): void { $this->tokensOutput=$v; }
    public function getTokensCacheRead(): int { return $this->tokensCacheRead; }
    public function setTokensCacheRead(int $v): void { $this->tokensCacheRead=$v; }
    public function getTokensCacheWrite(): int { return $this->tokensCacheWrite; }
    public function setTokensCacheWrite(int $v): void { $this->tokensCacheWrite=$v; }
    public function getCostUsd(): ?string { return $this->costUsd; }
    public function setCostUsd(?string $v): void { $this->costUsd=$v; }
    public function isRated(): bool { return $this->rated; }
    public function setRated(bool $v): void { $this->rated=$v; }
}
