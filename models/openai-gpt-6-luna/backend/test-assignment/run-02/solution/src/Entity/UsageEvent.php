<?php

declare(strict_types=1);

namespace App\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'usage_events')]
#[ORM\UniqueConstraint(name: 'usage_event_provider_request_unique', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'usage_events_unrated_idx', columns: ['id'], options: ['where' => '(cost_usd IS NULL)'])]
#[ORM\Index(name: 'usage_events_report_idx', columns: ['occurred_at'], options: ['where' => '(cost_usd IS NOT NULL)'])]
class UsageEvent
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: 'bigint')]
    public ?string $id = null;

    #[ORM\Column(name: 'provider_code', type: 'string', length: 120)]
    public string $providerCode;

    #[ORM\Column(name: 'provider_request_id', type: 'string', length: 255)]
    public string $providerRequestId;

    #[ORM\Column(name: 'model_code', type: 'string', length: 120)]
    public string $modelCode;

    #[ORM\Column(name: 'user_id', type: 'string', length: 255)]
    public string $userId;

    #[ORM\Column(name: 'occurred_at', type: 'datetimetz_immutable', precision: 6)]
    public DateTimeImmutable $occurredAt;

    #[ORM\Column(name: 'tokens_input', type: 'bigint')]
    public string $tokensInput;

    #[ORM\Column(name: 'tokens_output', type: 'bigint')]
    public string $tokensOutput;

    #[ORM\Column(name: 'tokens_cache_read', type: 'bigint')]
    public string $tokensCacheRead;

    #[ORM\Column(name: 'tokens_cache_write', type: 'bigint')]
    public string $tokensCacheWrite;

    #[ORM\Column(name: 'cost_usd', type: 'decimal', precision: 100, scale: 6, nullable: true)]
    public ?string $costUsd = null;

    #[ORM\Column(name: 'price_valid_from', type: 'datetimetz_immutable', precision: 6, nullable: true)]
    public ?DateTimeImmutable $priceValidFrom = null;
}
