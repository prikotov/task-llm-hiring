<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine\Entity;

use App\Domain\Usage\TokenUsage;
use App\Domain\Usage\UsageEvent;
use DateTimeImmutable;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования. Естественный ключ (provider_code, provider_request_id)
 * защищён уникальным индексом — на нём же строится идемпотентность приёма.
 *
 * Стоимость события хранится здесь же (cost_usd) и заполняется тарификацией.
 * NULL в cost_usd = событие ещё не рассчитано (unrated либо ещё не обработано).
 * Отдельный флаг не нужен: пустая стоимость и есть «не рассчитано».
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_usage_events_key', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_ts', columns: ['occurred_at'])]
#[ORM\Index(name: 'idx_usage_events_pending', columns: ['cost_usd'])]
final class UsageEventEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private ?int $id = null;

    #[ORM\Column(name: 'provider_code', type: 'string', length: 64)]
    private string $providerCode;

    #[ORM\Column(name: 'provider_request_id', type: 'string', length: 190)]
    private string $providerRequestId;

    #[ORM\Column(name: 'model_code', type: 'string', length: 128)]
    private string $modelCode;

    #[ORM\Column(name: 'user_ref', type: 'string', length: 190)]
    private string $userRef;

    #[ORM\Column(name: 'occurred_at', type: Types::DATETIME_IMMUTABLE)]
    private DateTimeImmutable $occurredAt;

    #[ORM\Column(name: 'tokens_input', type: 'bigint')]
    private int $tokensInput;

    #[ORM\Column(name: 'tokens_output', type: 'bigint')]
    private int $tokensOutput;

    #[ORM\Column(name: 'tokens_cache_read', type: 'bigint')]
    private int $tokensCacheRead;

    #[ORM\Column(name: 'tokens_cache_write', type: 'bigint')]
    private int $tokensCacheWrite;

    /** Для аудита: какая строка цен применилась при последнем расчёте. */
    #[ORM\Column(name: 'price_row_id', type: 'integer', nullable: true)]
    private ?int $priceRowId = null;

    #[ORM\Column(name: 'cost_usd', type: 'decimal', precision: 24, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    public function __construct(UsageEvent $event)
    {
        $this->providerCode = $event->providerCode;
        $this->providerRequestId = $event->providerRequestId;
        $this->modelCode = $event->modelCode;
        $this->userRef = $event->userRef;
        $this->occurredAt = $event->occurredAt;
        $this->tokensInput = $event->tokens->input;
        $this->tokensOutput = $event->tokens->output;
        $this->tokensCacheRead = $event->tokens->cacheRead;
        $this->tokensCacheWrite = $event->tokens->cacheWrite;
    }

    public function toDomain(): UsageEvent
    {
        return new UsageEvent(
            $this->providerCode,
            $this->providerRequestId,
            $this->modelCode,
            $this->userRef,
            $this->occurredAt,
            new TokenUsage($this->tokensInput, $this->tokensOutput, $this->tokensCacheRead, $this->tokensCacheWrite),
            $this->id,
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function providerCode(): string
    {
        return $this->providerCode;
    }

    public function providerRequestId(): string
    {
        return $this->providerRequestId;
    }

    public function modelCode(): string
    {
        return $this->modelCode;
    }

    public function userRef(): string
    {
        return $this->userRef;
    }

    public function occurredAt(): DateTimeImmutable
    {
        return $this->occurredAt;
    }

    public function tokens(): TokenUsage
    {
        return new TokenUsage($this->tokensInput, $this->tokensOutput, $this->tokensCacheRead, $this->tokensCacheWrite);
    }

    public function costUsd(): ?string
    {
        return $this->costUsd;
    }

    public function priceRowId(): ?int
    {
        return $this->priceRowId;
    }

    public function isRated(): bool
    {
        return null !== $this->costUsd;
    }

    public function applyPrice(?int $priceRowId, ?string $costUsd): void
    {
        $this->priceRowId = $priceRowId;
        $this->costUsd = $costUsd;
    }
}
