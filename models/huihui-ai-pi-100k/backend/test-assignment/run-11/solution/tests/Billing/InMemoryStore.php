<?php

declare(strict_types=1);

namespace App\Tests\Billing;

use App\Domain\Billing\Cost;
use App\Domain\Billing\RatedEventStore;
use App\Domain\Usage\UsageEvent;

/**
 * In-memory реализация RatedEventStore для юнит-тестов.
 *
 * @internal
 */
final class InMemoryStore implements RatedEventStore
{
    /** @var array<int, UsageEvent> */
    public array $saved = [];
    /** @var array<int, Cost> */
    public array $costs = [];

    public function saveRated(UsageEvent $event, Cost $cost): void
    {
        $this->saved[] = $event;
        $this->costs[] = $cost;
    }
}
