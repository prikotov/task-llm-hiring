<?php

declare(strict_types=1);

namespace App\Billing\Infrastructure\Persistence\Doctrine\Entity;

use App\Billing\Domain\Model\ModelReference;
use App\Billing\Domain\Money\Money;
use App\Billing\Domain\Usage\Rating;
use App\Billing\Domain\Usage\TokenUsage;
use App\Billing\Domain\Usage\UsageEvent;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования. Внешних ключей на справочник моделей и провайдеров
 * намеренно нет: событие — это факт от провайдера, и он должен сохраниться,
 * даже если модель ещё не появилась в справочнике (тогда событие unrated).
 *
 * Дедупликация — уникальный индекс (provider_code, provider_request_id).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_event')]
#[ORM\UniqueConstraint(name: 'uniq_usage_event_natural_key', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_event_unrated', columns: ['rated_at'])]
#[ORM\Index(name: 'idx_usage_event_ts', columns: ['ts'])]
#[ORM\Index(name: 'idx_usage_event_report', columns: ['user_id', 'provider_code', 'model_code'])]
final class UsageEventRecord
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::BIGINT)]
    private ?int $id = null;

    #[ORM\Column(name: 'provider_code', type: Types::STRING, length: 64)]
    private string $providerCode;

    #[ORM\Column(name: 'provider_request_id', type: Types::STRING, length: 255)]
    private string $providerRequestId;

    #[ORM\Column(name: 'model_code', type: Types::STRING, length: 128)]
    private string $modelCode;

    #[ORM\Column(name: 'user_id', type: Types::STRING, length: 255)]
    private string $userId;

    #[ORM\Column(name: 'ts', type: Types::DATETIMETZ_IMMUTABLE)]
    private \DateTimeImmutable $ts;

    #[ORM\Column(name: 'tokens_input', type: Types::BIGINT)]
    private int $tokensInput;

    #[ORM\Column(name: 'tokens_output', type: Types::BIGINT)]
    private int $tokensOutput;

    #[ORM\Column(name: 'tokens_cache_read', type: Types::BIGINT)]
    private int $tokensCacheRead;

    #[ORM\Column(name: 'tokens_cache_write', type: Types::BIGINT)]
    private int $tokensCacheWrite;

    /** Итоговая стоимость события, уже округлённая до 6 знаков (или NULL — unrated). */
    #[ORM\Column(name: 'cost_usd', type: Types::DECIMAL, precision: 30, scale: 6, nullable: true)]
    private ?string $costUsd = null;

    #[ORM\Column(name: 'price_row_id', type: Types::INTEGER, nullable: true)]
    private ?int $priceRowId = null;

    #[ORM\Column(name: 'rated_at', type: Types::DATETIMETZ_IMMUTABLE, nullable: true)]
    private ?\DateTimeImmutable $ratedAt = null;

    public static function fromDomain(UsageEvent $event): self
    {
        $record = new self();
        $record->providerCode = $event->model->providerCode;
        $record->providerRequestId = $event->providerRequestId;
        $record->modelCode = $event->model->modelCode;
        $record->userId = $event->user;
        $record->ts = $event->occurredAt;
        $record->tokensInput = $event->usage->input->value;
        $record->tokensOutput = $event->usage->output->value;
        $record->tokensCacheRead = $event->usage->cacheRead->value;
        $record->tokensCacheWrite = $event->usage->cacheWrite->value;

        return $record;
    }

    public function applyRating(?Rating $rating): void
    {
        $this->costUsd = $rating?->cost->toString();
        $this->priceRowId = $rating?->priceRowId;
        $this->ratedAt = $rating?->ratedAt;
    }

    public function toDomain(): UsageEvent
    {
        $rating = null;

        if (null !== $this->costUsd && null !== $this->ratedAt) {
            $rating = new Rating(Money::fromString($this->costUsd), $this->priceRowId, $this->ratedAt);
        }

        return new UsageEvent(
            $this->id,
            $this->providerRequestId,
            new ModelReference($this->providerCode, $this->modelCode),
            $this->userId,
            $this->ts,
            TokenUsage::of($this->tokensInput, $this->tokensOutput, $this->tokensCacheRead, $this->tokensCacheWrite),
            $rating,
        );
    }

    public function id(): ?int
    {
        return $this->id;
    }

    public function priceRowId(): ?int
    {
        return $this->priceRowId;
    }

    public function ratedAt(): ?\DateTimeImmutable
    {
        return $this->ratedAt;
    }
}
