<?php

declare(strict_types=1);

namespace App\Domain\Billing;

/**
 * Состояние события: ещё не рассчитано (ожидает цену) / рассчитано.
 */
enum EventStatus: string
{
    case Pending = 'pending';
    case Rated = 'rated';
}
