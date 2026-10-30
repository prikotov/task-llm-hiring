<?php

declare(strict_types=1);

namespace App\Domain\Model;

use Doctrine\ORM\Mapping as ORM;

/**
 * Результат тарификации одного события.
 *
 * status: 'rated' или 'unrated'. unrated-события присутствуют здесь для
 * пересчёта при появлении цены (повторная попытка).
 */
#[ORM\Entity]
#[ORM\Table(name: 'usage_records')]
#[ORM\Index(name: 'rec_lookup', columns: ['provider_code', 'provider_request_id'])]
class UsageRecord
{
    public const STATUS_RATED = 'rated';
    public const STATUS_UNRATED = 'unrated';
    public const STATUS_PENDING = 'pending';

    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public readonly string $providerCode,

        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public readonly string $providerRequestId,

        #[ORM\Column(type: 'string')]
        public readonly string $status,

        #[ORM\Column(type: 'decimal', precision: 20, scale: 6, nullable: true)]
        public ?string $cost,
    ) {
    }
}
