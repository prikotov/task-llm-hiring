<?php

declare(strict_types=1);

namespace App\Billing\Entity;

#[\Doctrine\ORM\Mapping\Entity]
#[\Doctrine\ORM\Mapping\Table(name: 'rated_events')]
#[\Doctrine\ORM\Mapping\UniqueConstraint(name: 'uniq_rated_event', columns: ['event_id'])]
#[\Doctrine\ORM\Mapping\Index(name: 'idx_rated_ts', columns: ['event_ts', 'user_name'])]
#[\Doctrine\ORM\Mapping\Index(name: 'idx_rated_price_row', columns: ['price_row_id'])]
class RatedEvent
{
    /**
     * Event id совпадает с usage_events.id (<provider>\u0001<requestId>).
     */
    public function __construct(
        #[\Doctrine\ORM\Mapping\Id]
        #[\Doctrine\ORM\Mapping\Column(name: 'event_id', type: 'string', length: 200)]
        #[\Doctrine\ORM\Mapping\GeneratedValue(strategy: 'NONE')]
        public readonly string $eventId,
    ) {}

    #[\Doctrine\ORM\Mapping\Column(name: 'price_row_id', type: 'string', length: 255)]
    public string $priceRowId = '';

    #[\Doctrine\ORM\Mapping\Column(name: 'event_ts', type: 'datetime_immutable')]
    public \DateTimeImmutable $eventTs;

    #[\Doctrine\ORM\Mapping\Column(name: 'user_name', type: 'string', length: 128)]
    public string $user = '';

    /** @var array<int> (input, output, cache_read, cache_write) */
    #[\Doctrine\ORM\Mapping\Column(type: 'json')]
    public array $tokens = [0, 0, 0, 0];

    /**
     * Итоговая стоимость события (USD), десятичная строка.
     * При выводе форматируется ровно 6 знаков (Money::formatMicros).
     */
    #[\Doctrine\ORM\Mapping\Column(name: 'cost_usd', type: 'string', length: 32)]
    public string $costUsd = '0';

    #[\Doctrine\ORM\Mapping\Column(name: 'created_at', type: 'datetime_immutable')]
    public \DateTimeImmutable $createdAt;

    /** Последний peresчёт; null — первичный счёт. */
    #[\Doctrine\ORM\Mapping\Column(name: 'repriced_at', type: 'datetime_immutable', nullable: true)]
    public ?\DateTimeImmutable $repricedAt = null;
}
