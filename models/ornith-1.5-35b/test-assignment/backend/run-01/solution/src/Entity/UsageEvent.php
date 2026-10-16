<?php

declare(strict_types=1);

namespace App\Entity;

use App\Domain\Code;
use App\Domain\Instant;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_event')]
#[ORM\UniqueConstraint('usage_event_provider_reqid_unique', ['provider_id', 'provider_request_id'])]
/**
 * Событие использования (фактический запрос к модели).
 * Естественный ключ: (provider_id, provider_request_id).
 */
final class UsageEvent
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    private Provider $provider;

    #[ORM\Column(type: 'string', length: 128)]
    private string $providerRequestId;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    private Model $model;

    #[ORM\Column(type: 'string', length: 128)]
    private string $user;

    #[ORM\Column(type: 'datetime_immutable')]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: 'integer')]
    private int $tokensInput = 0;

    #[ORM\Column(type: 'integer')]
    private int $tokensOutput = 0;

    #[ORM\Column(type: 'integer')]
    private int $tokensCacheRead = 0;

    #[ORM\Column(type: 'integer')]
    private int $tokensCacheWrite = 0;

    public function __construct(
        Provider $provider,
        string $providerRequestId,
        Model $model,
        string $user,
        \DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite
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

    public function getProvider(): Provider
    {
        return $this->provider;
    }

    public function getRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function getKey(): string
    {
        return $this->provider->getCode() . '/' . $this->providerRequestId;
    }

    public function getCode(): Code
    {
        return Code::fromString($this->providerRequestId);
    }

    public function getModel(): Model
    {
        return $this->model;
    }

    public function getUser(): Code
    {
        return Code::fromString($this->user);
    }

    public function getInstant(): Instant
    {
        return Instant::fromString($this->ts->format(\DateTimeInterface::ATOM));
    }

    public function getTokens(): array
    {
        return [
            $this->tokensInput,
            $this->tokensOutput,
            $this->tokensCacheRead,
            $this->tokensCacheWrite,
        ];
    }
}
