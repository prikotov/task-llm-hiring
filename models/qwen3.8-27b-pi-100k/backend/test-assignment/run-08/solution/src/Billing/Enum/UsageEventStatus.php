<?php

declare(strict_types=1);

namespace App\Billing\Enum;

/**
 * Статус события использования.
 */
enum UsageEventStatus: string
{
    /** Принято, ещё не рассчитано (в т.ч. unrated — повторные попытки на каждом запуске rate). */
    case Pending = 'pending';

    /** Рассчитано: стоимость зафиксирована. */
    case Rated = 'rated';
}
