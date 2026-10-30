<?php

namespace App\Entity;

use App\Repository\UsageEventRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UsageEventRepository::class)]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'idx_provider_request_id', columns: ['provider_code', 'provider_request_id'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_code', referencedColumnName: 'code', nullable: false)]
    private Provider $provider;

    #[ORM\Column(type: 'string')]
    private string $providerRequestId;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    private Model $model;

    #[ORM\Column(type: 'string')]
    private string $user;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: 'bigint')]
    private string $tokensInput;

    #[ORM\Column(type: 'bigint')]
    private string $tokensOutput;

    #[ORM\Column(type: 'bigint')]
    private string $tokensCacheRead;

    #[ORM\Column(type: 'bigint')]
    private string $tokensCacheWrite;

    #[ORM\Column(type: 'boolean')]
    private bool $isRated = false;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $costUsd = null;

    public function __construct(
        Provider $provider,
        string $providerRequestId,
        Model $model,
        string $user,
        \DateTimeImmutable $ts,
        string $tokensInput,
        string $tokensOutput,
        string $tokensCacheRead,
        string $tokensCacheWrite
    ) {
        $this->provider = $provider;
        $this->providerRequestId = $providerRequestId;
        $this->model = $model;
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

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getProviderRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getUser(): string
    {
        return $this->user;
    }

    public function getTs(): \DateTimeImmutable
    {
        return $this->ts;
    }

    public function getTokensInput(): string
    {
        return $this->tokensInput;
    }

    public function getTokensOutput(): string
    {
        return $this->tokensOutput;
    }

    public function getTokensCacheRead(): string
    {
        return $this->tokensCacheRead;
    }

    public function getTokensCacheWrite(): string
    {
        return $this->tokensCacheWrite;
    }

    public function isRated(): bool
    {
        return $this->isRated;
    }

    public function setRated(bool $isRated): void
    {
        $this->isRated = $isRated;
    }

    public function getCostUsd(): ?string
    {
        return $this->costUsd;
    }

    public function setCostUsd(string $costUsd): void
    {
        $this->costUsd = $costUsd;
    }
}
