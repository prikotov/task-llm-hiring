<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\UsageEventRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

/**
 * Событие использования (запрос к модели), поставленное провайдером.
 *
 * Натуральный ключ — (providerCode, providerRequestId). Справки провайдеров
 * и моделей хранятся как коды без FK-связи: событие может прийти раньше,
 * чем справочник по нему подтянулся (см. допущения в PR_DESCRIPTION.md).
 * Стоимость в этом классе не хранится — только факт использования.
 */
#[ORM\Entity(repositoryClass: UsageEventRepository::class)]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uq_usage_events_provider_request', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'ix_usage_events_provider_model', columns: ['provider_code', 'model_code'])]
#[ORM\Index(name: 'ix_usage_events_ts', columns: ['ts'])]
final class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'provider_code', type: Types::STRING, length: 128)]
    private string $providerCode;

    #[ORM\Column(name: 'provider_request_id', type: Types::STRING, length: 255)]
    private string $providerRequestId;

    #[ORM\Column(name: 'model_code', type: Types::STRING, length: 128)]
    private string $modelCode;

    #[ORM\Column(name: 'user_login', type: Types::STRING, length: 255)]
    private string $user;

    #[ORM\Column(type: Types::DATETIMETZ_IMMUTABLE)]
    private DateTimeImmutable $ts;

    #[ORM\Column(name: 'tokens_input', type: Types::BIGINT)]
    private int $tokensInput = 0;

    #[ORM\Column(name: 'tokens_output', type: Types::BIGINT)]
    private int $tokensOutput = 0;

    #[ORM\Column(name: 'tokens_cache_read', type: Types::BIGINT)]
    private int $tokensCacheRead = 0;

    #[ORM\Column(name: 'tokens_cache_write', type: Types::BIGINT)]
    private int $tokensCacheWrite = 0;

    #[ORM\Column(name: 'received_at', type: Types::DATETIMETZ_IMMUTABLE)]
    private DateTimeImmutable $receivedAt;

    public function __construct(
        string $providerCode,
        string $providerRequestId,
        string $modelCode,
        string $user,
        DateTimeImmutable $ts,
        int $tokensInput,
        int $tokensOutput,
        int $tokensCacheRead,
        int $tokensCacheWrite,
        DateTimeImmutable $receivedAt,
    ) {
        $this->providerCode = $providerCode;
        $this->providerRequestId = $providerRequestId;
        $this->modelCode = $modelCode;
        $this->user = $user;
        $this->ts = $ts->setTimezone(new \DateTimeZone('UTC'));
        $this->tokensInput = $tokensInput;
        $this->tokensOutput = $tokensOutput;
        $this->tokensCacheRead = $tokensCacheRead;
        $this->tokensCacheWrite = $tokensCacheWrite;
        $this->receivedAt = $receivedAt->setTimezone(new \DateTimeZone('UTC'));
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProviderCode(): string
    {
        return $this->providerCode;
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

    public function getTs(): DateTimeImmutable
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
}
