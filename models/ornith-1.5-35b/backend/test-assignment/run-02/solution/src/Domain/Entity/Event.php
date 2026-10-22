<?php

declare(strict_types=1);

namespace App\Domain\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'event')]
#[ORM\UniqueConstraint(
    columns: ['provider', 'provider_request_id'],
    name: 'event_provider_request_unique',
)]
final class Event
{
    public function __construct(
        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public string $provider,

        #[ORM\ManyToOne(targetEntity: Provider::class, fetch: 'EAGER')]
        #[ORM\JoinColumn(name: 'provider_id', referencedColumnName: 'provider_code', nullable: false)]
        public Provider $dataProvider,

        #[ORM\Id]
        #[ORM\Column(type: 'string')]
        public string $providerRequestId,

        #[ORM\ManyToOne(targetEntity: Model::class, fetch: 'EAGER')]
        #[ORM\JoinColumn(name: 'model_code', referencedColumnName: 'code', nullable: false)]
        public Model $model,

        #[ORM\Column(type: 'string')]
        public string $user,

        #[ORM\Column(type: 'datetime_immutable')]
        public DateTimeImmutable $timestamp,

        /// Canonical label used in reports, e.g. prism/lumen-8.
        #[ORM\Column(type: 'string', name: 'rating_date')]
        public string $ratingDate,

        #[ORM\Column(type: 'bigint', nullable: true, name: 'tokens_input')]
        public ?int $tokensInput,

        #[ORM\Column(type: 'bigint', nullable: true, name: 'tokens_output')]
        public ?int $tokensOutput,

        #[ORM\Column(type: 'bigint', nullable: true, name: 'tokens_cache_read')]
        public ?int $tokensCacheRead,

        #[ORM\Column(type: 'bigint', nullable: true, name: 'tokens_cache_write')]
        public ?int $tokensCacheWrite,

        /// Cost of the event as currently counted in the billing total. 0 when unrated.
        #[ORM\Column(type: 'decimal', name: 'total_cost', precision: 20, scale: 6)] public string $totalCost,

        /// Cost of the event when first rated (base for reprice deltas).
        #[ORM\Column(type: 'decimal', name: 'original_cost', precision: 20, scale: 6)] public string $originalCost,

        /// Whether the event has been rated and counts in the billing totals.
        #[ORM\Column(type: 'boolean', name: 'is_rated', nullable: false)] public bool $isRated = false,
    ) {
    }

    /** All four token counts in canonical order, nulls coerced to zero. */
    public function tokenCounts(): array
    {
        return [
            'input' => $this->tokensInput ?? 0,
            'output' => $this->tokensOutput ?? 0,
            'cache_read' => $this->tokensCacheRead ?? 0,
            'cache_write' => $this->tokensCacheWrite ?? 0,
        ];
    }
}
