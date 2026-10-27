<?php

declare(strict_types=1);

namespace App\Billing\Entity;

use App\Billing\Enum\UsageEventStatus;
use App\Billing\ValueObject\Decimal;
use App\Billing\ValueObject\TokenUsage;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

/**
 * Событие использования (один запрос к модели), поставленное провайдером.
 *
 * Естественный ключ (дедупликация) — (provider, provider_request_id):
 * повторная доставка того же ключа игнорируется (обновлений и доначислений нет).
 *
 * Стоимость хранится как десятичная строка (bcmath, без float).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\Index(name: 'idx_usage_events_status', columns: ['status'])]
#[ORM\Index(name: 'idx_usage_events_ts', columns: ['ts'])]
final class UsageEvent
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 64)]
    public string $provider;

    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 200)]
    public string $providerRequestId;

    #[ORM\Column(type: 'string', length: 64)]
    public string $model;

    #[ORM\Column(name: 'user_id', type: 'string', length: 255)]
    public string $user;

    #[ORM\Column(type: 'datetime_immutable')]
    public DateTimeImmutable $ts;

    #[ORM\Column(type: 'bigint')]
    public int $tokensInput;

    #[ORM\Column(type: 'bigint')]
    public int $tokensOutput;

    #[ORM\Column(type: 'bigint')]
    public int $tokensCacheRead;

    #[ORM\Column(type: 'bigint')]
    public int $tokensCacheWrite;

    /** @see UsageEventStatus */
    #[ORM\Column(type: 'string', length: 16)]
    public string $status;

    /** Стоимость события в USD: ровно 6 знаков после округления (HALF_UP), либо null, пока unrated. */
    #[ORM\Column(type: 'string', length: 32, nullable: true)]
    public ?string $cost = null;

    /** id строки цен, которой событие было рассчитано (для ретро-коррекции). */
    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $priceRowId = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    public ?DateTimeImmutable $ratedAt = null;

    #[ORM\Column(type: 'datetime_immutable')]
    public DateTimeImmutable $importedAt;

    public function __construct(
        string $provider,
        string $providerRequestId,
        string $model,
        string $user,
        DateTimeImmutable $ts,
        TokenUsage $tokens,
        DateTimeImmutable $importedAt,
    ) {
        $this->provider = $provider;
        $this->providerRequestId = $providerRequestId;
        $this->model = $model;
        $this->user = $user;
        $this->ts = $ts;
        $this->tokensInput = $tokens->input;
        $this->tokensOutput = $tokens->output;
        $this->tokensCacheRead = $tokens->cacheRead;
        $this->tokensCacheWrite = $tokens->cacheWrite;
        $this->status = UsageEventStatus::Pending->value;
        $this->importedAt = $importedAt;
    }

    public function isRated(): bool
    {
        return $this->status === UsageEventStatus::Rated->value;
    }

    public function usage(): TokenUsage
    {
        return new TokenUsage($this->tokensInput, $this->tokensOutput, $this->tokensCacheRead, $this->tokensCacheWrite);
    }

    /**
     * Фиксирует расчёт: стоимость (уже с окончательным округлением до 6 знаков) и ссылка на строку цен.
     */
    public function markRated(Decimal $cost, int $priceRowId, DateTimeImmutable $at): void
    {
        $this->status = UsageEventStatus::Rated->value;
        $this->cost = $cost->toFixed(6);
        $this->priceRowId = $priceRowId;
        $this->ratedAt = $at;
    }
}
