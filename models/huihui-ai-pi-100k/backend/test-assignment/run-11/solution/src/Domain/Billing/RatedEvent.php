<?php

declare(strict_types=1);

namespace App\Domain\Billing;

use App\Domain\Usage\UsageEvent;

/**
 * Результат тарификации одного события (event + его рассчитанная стоимость).
 */
final class RatedEvent
{
    public function __construct(
        public readonly UsageEvent $event,
        public readonly Cost $cost,
    ) {
    }
}
