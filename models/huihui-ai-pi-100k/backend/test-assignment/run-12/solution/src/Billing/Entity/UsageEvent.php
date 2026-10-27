<?php

declare(strict_types=1);

namespace App\Billing\Entity;

#[\Doctrine\ORM\Mapping\Entity]
#[\Doctrine\ORM\Mapping\Table(name: 'usage_events')]
#[\Doctrine\ORM\Mapping\UniqueConstraint(name: 'uniq_event', columns: ['provider_code', 'request_id'])]
#[\Doctrine\ORM\Mapping\Index(name: 'idx_event_ts', columns: ['ts'])]
#[\Doctrine\ORM\Mapping\Index(name: 'idx_event_rating', columns: ['is_rated', 'ts'])]
class UsageEvent
{
    /** Пустая string; <providerCode>\u0001<requestId>. */
    public function __construct(
        #[\Doctrine\ORM\Mapping\Id]
        #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 200)]
        #[\Doctrine\ORM\Mapping\GeneratedValue(strategy: 'NONE')]
        public readonly string $id,
    ) {}

    #[\Doctrine\ORM\Mapping\Column(name: 'provider_code', type: 'string', length: 64)]
    public string $providerCode = '';

    #[\Doctrine\ORM\Mapping\Column(name: 'request_id', type: 'string', length: 128)]
    public string $requestId = '';

    #[\Doctrine\ORM\Mapping\Column(type: 'string', length: 128)]
    public string $modelCode = '';

    #[\Doctrine\ORM\Mapping\Column(name: 'user_name', type: 'string', length: 128)]
    public string $user = '';

    #[\Doctrine\ORM\Mapping\Column(type: 'datetime_immutable')]
    public \DateTimeImmutable $ts;

    /** @var array<int> (input, output, cache_read, cache_write) */
    #[\Doctrine\ORM\Mapping\Column(type: 'json')]
    public array $tokens = [0, 0, 0, 0];

    /** Признак «уже оценено, есть rated row» (denormalized, для быстрых фильтров). */
    #[\Doctrine\ORM\Mapping\Column(name: 'is_rated', type: 'boolean', options: ['default' => 0])]
    public bool $isRated = false;
}
