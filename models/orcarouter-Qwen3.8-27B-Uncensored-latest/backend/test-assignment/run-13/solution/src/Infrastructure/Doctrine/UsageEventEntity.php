<?php

declare(strict_types=1);

namespace App\Infrastructure\Doctrine;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\DBAL\Types\Types;

#[ORM\Entity]
#[ORM\Table(name: 'billing_usage_events')]
#[ORM\UniqueConstraint(name: 'uq_usage_event', columns: ['provider_code', 'provider_request_id'])]
#[ORM\Index(name: 'idx_usage_ts', columns: ['ts_utc'])]
class UsageEventEntity
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    public int $id;

    #[ORM\Column(type: Types::STRING, length: 64)]
    public string $providerCode;

    #[ORM\Column(type: Types::STRING, length: 128)]
    public string $providerRequestId;

    #[ORM\Column(type: Types::STRING, length: 128, name: 'user_name')]
    public string $user;

    /** UTC timestamp */
    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    public \DateTimeImmutable $tsUtc;

    #[ORM\Column(type: Types::STRING, length: 128)]
    public string $modelCode;

    #[ORM\Column(type: Types::INTEGER)]
    public int $tokensInput    = 0;

    #[ORM\Column(type: Types::INTEGER)]
    public int $tokensOutput   = 0;

    #[ORM\Column(type: Types::INTEGER)]
    public int $tokensCacheRead  = 0;

    #[ORM\Column(type: Types::INTEGER)]
    public int $tokensCacheWrite = 0;

    // rating state: 'pending' | 'rated' | 'unrated'
    #[ORM\Column(type: Types::STRING, length: 16)]
    public string $ratingState = 'pending';

    #[ORM\Column(type: Types::STRING, length: 3, nullable: true)]
    public ?string $currency = null;

    #[ORM\Column(type: Types::STRING, length: 64, nullable: true)]
    public ?string $rateSpecUnit = null;

    #[ORM\Column(type: Types::STRING, length: 1024, nullable: true)]
    public ?string $rateSpecJson = null;

    /** exact unrounded cost (decimal string) */
    #[ORM\Column(type: Types::STRING, length: 512, nullable: true)]
    public ?string $exactCost = null;

    /** rounded cost to 6dp (decimal string) */
    #[ORM\Column(type: Types::STRING, length: 512, nullable: true)]
    public ?string $cost = null;

    #[ORM\Column(type: Types::BOOLEAN)]
    public bool $offPeakApplied = false;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE, nullable: true)]
    public ?\DateTimeImmutable $updatedAt = null;
}
