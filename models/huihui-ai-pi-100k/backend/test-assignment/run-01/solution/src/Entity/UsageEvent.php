<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\UniqueConstraint(name: 'uniq_event_provider_request', columns: ['provider_id', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_event_status', columns: ['status'])]
#[ORM\Index(name: 'idx_usage_event_occurred_at', columns: ['occurred_at'])]
#[ORM\Index(name: 'idx_usage_event_report', columns: ['user_name', 'status'])]
class UsageEvent
{
    public const STATUS_UNRATED = 'unrated';
    public const STATUS_RATED = 'rated';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    public int $id;

    #[ORM\ManyToOne(targetEntity: Provider::class)]
    #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'id', nullable: false)]
    public Provider $provider;

    /** Идентификатор запроса со стороны провайдера; (provider, id) — естественный ключ. */
    #[ORM\Column(type: 'string', length: 255)]
    public string $providerRequestId;

    #[ORM\ManyToOne(targetEntity: Model::class)]
    #[ORM\JoinColumn(name: 'model_id', referencedColumnName: 'id', nullable: false)]
    public Model $model;

    #[ORM\Column(name: 'user_name', type: 'string', length: 255)]
    public string $user;

    /** Момент запроса, UTC. */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $occurredAt;

    #[ORM\Column(type: 'integer')]
    public int $tokensInput = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensOutput = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensCacheRead = 0;

    #[ORM\Column(type: 'integer')]
    public int $tokensCacheWrite = 0;

    /** unrated | rated */
    #[ORM\Column(type: 'string', length: 16)]
    public string $status = self::STATUS_UNRATED;

    /**
     * Итоговая стоимость события, десятичная строка (6 знаков),
     * заполнена только для rated.
     */
    #[ORM\Column(type: 'string', length: 40, nullable: true)]
    public ?string $cost = null;

    /** Строка цен, которой тарифицировано событие (нужно для пересчёта). */
    #[ORM\ManyToOne(targetEntity: PriceRow::class)]
    #[ORM\JoinColumn(name: 'price_row_id', referencedColumnName: 'id', nullable: true)]
    public ?PriceRow $priceRow = null;

    /** Момент расчёта (аудит), заполнен при rated. */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    public ?\DateTimeImmutable $ratedAt = null;

    /** Время события в канонической ISO-8601 UTC. */
    public function occurredAtIso(): string
    {
        return $this->occurredAt->format('Y-m-d\TH:i:s\Z');
    }
}
