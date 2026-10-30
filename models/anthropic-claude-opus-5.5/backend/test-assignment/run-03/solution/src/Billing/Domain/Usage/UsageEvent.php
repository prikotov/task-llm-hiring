<?php

declare(strict_types=1);

namespace App\Billing\Domain\Usage;

use App\Billing\Domain\Model\ModelReference;
use App\Billing\Domain\Model\OffPeakWindow;
use App\Billing\Domain\Money\Money;

/**
 * Событие использования — факт запроса к модели. Неизменяемо по смыслу:
 * исправить историю нельзя, можно только переоценить (см. CostAdjustment).
 *
 * naturalKey = (provider, provider_request_id) — TASK п. 5.
 */
final readonly class UsageEvent
{
    public function __construct(
        public ?int $id,
        public string $providerRequestId,
        public ModelReference $model,
        public string $user,
        public \DateTimeImmutable $occurredAt,
        public TokenUsage $usage,
        public ?Rating $rating = null,
    ) {
        if ('' === trim($providerRequestId)) {
            throw new \InvalidArgumentException('provider_request_id обязателен');
        }

        if ('' === trim($user)) {
            throw new \InvalidArgumentException('Идентификатор пользователя обязателен');
        }
    }

    /**
     * Ключ дедупликации. Совпадение id у разных провайдеров — разные события,
     * поэтому провайдер входит в ключ.
     */
    public function naturalKey(): string
    {
        return $this->model->providerCode."\0".$this->providerRequestId;
    }

    public function withId(int $id): self
    {
        return new self(
            $id,
            $this->providerRequestId,
            $this->model,
            $this->user,
            $this->occurredAt,
            $this->usage,
            $this->rating,
        );
    }

    public function withRating(?Rating $rating): self
    {
        return new self(
            $this->id,
            $this->providerRequestId,
            $this->model,
            $this->user,
            $this->occurredAt,
            $this->usage,
            $rating,
        );
    }

    public function isRated(): bool
    {
        return null !== $this->rating;
    }
}

/**
 * Результат тарификации события: чем и почём. Хранит стоимость (уже округлённую
 * до 6 знаков) и ссылку на строку цен, чтобы при ретро-коррекции понять,
 * что именно надо пересчитать.
 */
final readonly class Rating
{
    public function __construct(
        public Money $cost,
        public ?int $priceRowId,
        public \DateTimeImmutable $ratedAt,
        public ?OffPeakWindow $appliedOffPeak = null,
    ) {
    }

    public function withCost(Money $cost): self
    {
        return new self($cost, $this->priceRowId, $this->ratedAt, $this->appliedOffPeak);
    }

    public function wasRepricedTo(Money $cost, \DateTimeImmutable $at): self
    {
        return new self($cost, $this->priceRowId, $at, $this->appliedOffPeak);
    }
}
