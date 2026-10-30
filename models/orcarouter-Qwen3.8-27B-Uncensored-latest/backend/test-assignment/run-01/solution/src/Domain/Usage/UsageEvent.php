<?php

declare(strict_types=1);

namespace App\Domain\Usage;

use App\Domain\Billing\Money;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'uniq_usage_events_provider_request', columns: ['provider', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_events_ts', columns: ['ts'])]
#[ORM\Index(name: 'idx_usage_events_status', columns: ['status'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'bigint')]
    public ?int $id = null;

    /** Код провайдера (идентификатор со стороны провайдера). */
    #[ORM\Column(type: 'string', length: 64)]
    public string $provider = '';

    /** provider_request_id — вместе с provider естественный ключ события. */
    #[ORM\Column(type: 'string', length: 255)]
    public string $providerRequestId = '';

    /** Код модели (denormalized, в системе счётов провайдера). */
    #[ORM\Column(type: 'string', length: 128)]
    public string $modelCode = '';

    #[ORM\Column(name: 'user_id', type: 'string', length: 255)]
    public string $user = '';

    /** Момент запроса, всегда хранится в UTC. */
    #[ORM\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $ts;

    #[ORM\Column(type: 'bigint')]
    public int $tokensInput = 0;

    #[ORM\Column(type: 'bigint')]
    public int $tokensOutput = 0;

    #[ORM\Column(type: 'bigint')]
    public int $tokensCacheRead = 0;

    #[ORM\Column(type: 'bigint')]
    public int $tokensCacheWrite = 0;

    #[ORM\Column(type: 'string', length: 16)]
    public string $status = EventStatus::Pending->value;

    /** id строки цен (price_rows.id), которой тарифицировано событие. */
    #[ORM\Column(type: 'integer', nullable: true)]
    public ?int $priceRowId = null;

    /** Итоговая стоимость события, 6 знаков (только для рассчитанных). */
    #[ORM\Column(type: 'decimal', precision: 34, scale: 6, nullable: true)]
    public ?string $costUsd = null;

    #[ORM\Column(type: 'datetime_immutable', nullable: true)]
    public ?\DateTimeImmutable $ratedAt = null;

    public function isPending(): bool
    {
        return $this->status === EventStatus::Pending->value;
    }

    public function markRated(int $priceRowId, string $costUsd, \DateTimeImmutable $at): void
    {
        $this->status = EventStatus::Rated->value;
        $this->priceRowId = $priceRowId;
        $this->costUsd = Money::format6(Money::roundHalfUpTo6($costUsd));
        $this->ratedAt = $at;
    }
}
