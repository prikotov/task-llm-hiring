<?php

declare(strict_types=1);

namespace App\Infrastructure;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'billing_usage_event')]
#[ORM\UniqueConstraint(name: 'uq_billing_usage_event', columns: ['provider', 'request_id'])]
#[ORM\Index(name: 'idx_billing_usage_event_status_ts', columns: ['status', 'ts'])]
final class UsageEvent
{
    public const STATUS_UNRATED = 'unrated';
    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\Column(type: Types::BIGINT)]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    private ?int $id = null;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $provider;

    #[ORM\Column(type: Types::STRING, length: 128)]
    private string $requestId;

    #[ORM\Column(type: Types::STRING, length: 64)]
    private string $model;

    #[ORM\Column(name: 'user_id', type: Types::STRING, length: 128)]
    private string $user;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    private \DateTimeImmutable $ts;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensInput;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensOutput;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensCacheRead;

    #[ORM\Column(type: Types::INTEGER)]
    private int $tokensCacheWrite;

    #[ORM\Column(type: Types::STRING, length: 16)]
    private string $status;

    public function __construct(
        string $provider,
        string $requestId,
        string $model,
        string $user,
        \DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
    ) {
        $this->provider = $provider;
        $this->requestId = $requestId;
        $this->model = $model;
        $this->user = $user;
        $this->ts = (clone $ts)->setTimezone(new \DateTimeZone('UTC'));
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
        $this->status = self::STATUS_UNRATED;
    }

    public function id(): ?int { return $this->id; }
    public function providerValue(): string { return $this->provider; }
    public function requestIdValue(): string { return $this->requestId; }
    public function modelValue(): string { return $this->model; }
    public function userValue(): string { return $this->user; }
    public function tsValue(): \DateTimeImmutable { return $this->ts; }
    public function tokensInputValue(): int { return $this->tokensInput; }
    public function tokensOutputValue(): int { return $this->tokensOutput; }
    public function tokensCacheReadValue(): int { return $this->tokensCacheRead; }
    public function tokensCacheWriteValue(): int { return $this->tokensCacheWrite; }
    public function statusValue(): string { return $this->status; }

    public function markRated(): void
    {
        $this->status = self::STATUS_RATED;
    }
}
